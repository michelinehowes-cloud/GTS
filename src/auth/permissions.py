class BasePermission:
    """Base permission class"""
    def has_permission(self, user):
        raise NotImplementedError


class IsAdminUser(BasePermission):
    """Permission check for admin users"""
    
    def has_permission(self, user):
        """
        Check if user has admin privileges
        """
        return user.is_authenticated and user.is_admin


class IsSuperUser(BasePermission):
    """Permission check for super users"""
    
    def has_permission(self, user):
        """
        Check if user has super user privileges
        """
        return user.is_authenticated and user.is_superuser


class PermissionManager:
    """Manages and checks permissions"""
    
    def __init__(self):
        self.permissions = {}
    
    def add_permission(self, name, permission_class):
        self.permissions[name] = permission_class
    
    def check_permission(self, user, permission_name):
        if permission_name not in self.permissions:
            return False
        return self.permissions[permission_name]().has_permission(user)