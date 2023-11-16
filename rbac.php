<?php

    class RBAC {
        public $roleId;
        public $role_name;
        public $permissionId;
        public $permission_name;
        public $conn;

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

        function checkPermissions($role_id){
            
        }
    }

    

?>