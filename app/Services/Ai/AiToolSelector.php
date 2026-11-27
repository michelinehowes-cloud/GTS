<?php

namespace App\Services\Ai;

/**
 * Picks the tools that are relevant to the user's question, so each request to the AI provider
 * carries ~12 tool descriptions instead of all of them. This keeps requests small enough for
 * free-tier token limits (e.g. Groq: 8,000 tokens per minute) and makes answers faster.
 */
class AiToolSelector
{
    /** Topic keywords (normalized Arabic / English) => words that appear in matching tool names */
    protected const TOPICS = [
        'تدريب|دوره|دورات|برنامج|برامج|ورشه|مقاعد|تسجيل في|التحاق|training|course' => ['training', 'trainings', 'application'],
        'وظيف|وظايف|عمل|فرص|فرصه|توظيف|شاغر|job|career' => ['job', 'jobs', 'opportunit', 'employment', 'apply', 'cover_letter'],
        'خريج|خريجين|طالب|طلاب|graduate' => ['graduate', 'graduates', 'nominate'],
        'شركه|شركات|شريك|شراكه|company|partner' => ['compan', 'partner', 'partnership'],
        'ترشيح|ترشح|مرشح|مرشحين|مقابل|candidate' => ['nominat', 'candidate', 'interview', 'matching'],
        'تقرير|احصا|قارن|مقارن|تحليل|حلل|مؤشر|نسب|ارقام|توصي|report|stat' => ['report', 'statistic', 'overview', 'analytics', 'stats', 'summary'],
        'خبر|اخبار|اعلان|اعلانات|تغطيه|اعلام|news|media' => ['news', 'announcement', 'media', 'coverage'],
        'استبيان|استبيانات|تقييم|survey|evaluation' => ['survey', 'evaluation'],
        'سيره|سير|ملفي|ملف|بياناتي|حسابي|profile|cv' => ['profile', 'my_', 'cover_letter', 'job_opportunities', 'trainings'],
        'كلمه المرور|كلمه السر|باسورد|password' => ['password'],
        'موقع المكتب|عنوان|تواصل|هاتف|خرائط|contact|location' => ['contact', 'location'],
        'تجميد|حذف|ايقاف|تفعيل|delete|toggle' => ['toggle', 'delete', 'status'],
    ];

    public static function select(array $tools, string $message, array $history = [], int $limit = 12): array
    {
        if (count($tools) <= $limit) {
            return $tools;
        }

        // The question plus the user's previous message (for follow-ups like "نعم، نفّذ ذلك")
        $context = $message;
        for ($i = count($history) - 1; $i >= 0; $i--) {
            if (($history[$i]['role'] ?? '') === 'user') {
                $context .= ' ' . mb_substr($history[$i]['content'], 0, 300);
                break;
            }
        }
        $text = self::normalize($context);
        $words = array_filter(explode(' ', $text), fn ($w) => mb_strlen($w) >= 3);

        $topicHints = [];
        foreach (self::TOPICS as $pattern => $hints) {
            if (preg_match('/' . $pattern . '/u', $text)) {
                $topicHints = array_merge($topicHints, $hints);
            }
        }

        $scored = [];
        foreach ($tools as $i => $tool) {
            $name = strtolower($tool['name']);
            $desc = self::normalize($tool['description'] ?? '');
            $score = 0;

            foreach ($topicHints as $hint) {
                if (str_contains($name, $hint)) {
                    $score += 3;
                }
            }
            foreach ($words as $w) {
                if (str_contains($desc, $w)) {
                    $score += 1;
                }
            }
            // Read-only tools are safe defaults when nothing matches
            if (str_starts_with($name, 'get_') || str_starts_with($name, 'search_')) {
                $score += 0.5;
            }

            $scored[] = ['i' => $i, 'score' => $score];
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score'] ?: $a['i'] <=> $b['i']);

        $picked = array_slice($scored, 0, $limit);
        usort($picked, fn ($a, $b) => $a['i'] <=> $b['i']); // keep the registry order

        return array_map(fn ($p) => $tools[$p['i']], $picked);
    }

    public static function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $text); // diacritics, tatweel
        $text = str_replace(['أ', 'إ', 'آ', 'ة', 'ى'], ['ا', 'ا', 'ا', 'ه', 'ي'], $text);
        $text = preg_replace('/[^\p{L}\p{N}\s_]+/u', ' ', $text);
        // Drop the Arabic definite article so "التدريب" matches "تدريب"
        $text = preg_replace('/(^|\s)(وال|بال|كال|فال|لل|ال)(?=\p{L}{3,})/u', '$1', $text);

        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}
