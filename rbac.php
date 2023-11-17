<?php

    class RBAC {
        public $dbServername = "localhost";
        public $dbUsername = "root";
        public $dbPassword = "";
        public $dbName = "gotit_db";
        public $dbPort = 3306;

        function getRoleIdFromName($role_name){

            $conn = mysqli_connect($this->dbServername ,$this->dbUsername,$this->dbPassword,$this->dbName);

            $roleId = 0;
            $role_query = "SELECT * FROM roles WHERE role_name='$role_name'";
            $result_role_query = mysqli_query($conn, $role_query) or die(mysqli_error($conn));
            if (mysqli_num_rows($result_role_query) > 0) {
                while($row = mysqli_fetch_assoc($result_role_query)){
                    $roleId = $row["ID"];
                }
            }

            $conn->close();
    
            return $roleId;
        }

        function getRoleNameFromId($role_id){
            $conn = mysqli_connect($this->dbServername ,$this->dbUsername,$this->dbPassword,$this->dbName);

            $role_name = "";
            $role_query = "SELECT * FROM roles WHERE ID='$role_id'";
            $result_role_query = mysqli_query($conn, $role_query) or die(mysqli_error($conn));
            if (mysqli_num_rows($result_role_query) > 0) {
                while($row = mysqli_fetch_assoc($result_role_query)){
                    $role_name = $row["role_name"];
                }
            }

            $conn->close();
    
            return $role_name;
        }

        function getPermissions($role_id){
            $conn = mysqli_connect($this->dbServername ,$this->dbUsername,$this->dbPassword,$this->dbName);

            $permissions = [];

            $role_permission_query = "SELECT * FROM role_permission WHERE roleId='$role_id'";

            $result_role_permission_query = mysqli_query($conn, $role_permission_query) or die(mysqli_error($conn));
            if (mysqli_num_rows($result_role_permission_query) > 0) {
                while($row = mysqli_fetch_assoc($result_role_permission_query)){
                    array_push($permissions, $this->getPermissionNameFromId($row['permissionId']));
                }
            }

            $conn->close();
    
            return $permissions;           
            
        }

        function getPermissionNameFromId($permission_id){
            $conn = mysqli_connect($this->dbServername ,$this->dbUsername,$this->dbPassword,$this->dbName);

            $permission_name = "";
            $permission_query = "SELECT * FROM permissions WHERE ID='$permission_id'";
            $result_permission_query = mysqli_query($conn, $permission_query) or die(mysqli_error($conn));
            if (mysqli_num_rows($result_permission_query) > 0) {
                while($row = mysqli_fetch_assoc($result_permission_query)){
                    $permission_name = $row["permission_name"];
                }
            }

            $conn->close();
    
            return $permission_name;
        }

        function hasPermission($permission_name, $permissions_array){
            return in_array($permission_name, $permissions_array);
        }
    }

    

?>