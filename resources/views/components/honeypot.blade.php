@if(config('security.honeypot.enabled', true))
    <div style="display:none !important; opacity:0; position:absolute; left:-9999px; width:0; height:0;" aria-hidden="true">
        <label for="{{ config('security.honeypot.field_name', '_hp_security_check') }}">لا تملأ هذا الحقل</label>
        <input type="text" 
               name="{{ config('security.honeypot.field_name', '_hp_security_check') }}" 
               id="{{ config('security.honeypot.field_name', '_hp_security_check') }}" 
               value="" 
               tabindex="-1" 
               autocomplete="off">
        <input type="hidden" 
               name="{{ config('security.honeypot.time_field', '_hp_time_token') }}" 
               value="{{ encrypt(microtime(true)) }}">
    </div>
@endif
