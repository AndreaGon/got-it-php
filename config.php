<?php
    require_once 'libraries/PhpRbac/src/PhpRbac/Rbac.php';

    use PhpRbac\Rbac;
    $rbac = new Rbac();
    
    // Create Roles
    $user_role = $rbac->Roles->add('user', 'User can add lost and found items');
    $admin_role = $rbac->Roles->add('admin', 'Admin can manage users');
    $superadmin_role = $rbac->Roles->add('superadmin', 'Superadmin can manage admins');

    //Create permissions for users
    $user_permission = $rbac->Permissions->add('add_lost_item', 'Can add lost item');

    //Create permissions for admin
    $admin_permission = $rbac->Permissions->add('deactivate_users', 'Can deactivate users');

    //Create permissions for superadmin
    $superadmin_permission = $rbac->Permissions->add('add_admin', 'Can add admin');

    // Assign Permission to Role
    $rbac->Roles->assign($user_role, $user_permission);
    $rbac->Roles->assign($admin_role, $admin_role);
    $rbac->Roles->assign($superadmin_role, $superadmin_role);
    
    // // Assign Role to User (The UserID is provided by the application's User Management System)
    //$rbac->Users->assign($role_id, 8);
?>