<?php
namespace Module\User\Model;

use Core\Database;
use PDOException;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Récupérer tous les rôles configurés avec leurs infos
    public function getAllRoles()
    {
        $this->db->query('SELECT role_id, text_code, badge_code FROM t_user_role');
        $res = $this->db->resultSet();
        $roles = [];
        if ($res) {
            foreach ($res as $r) {
                $roles[$r['role_id']] = is_object($r) ? (array)$r : $r;
            }
        }
        return $roles;
    }

    // Récupérer tous les statuts d'utilisateurs disponibles
    public function getUserStatuses()
    {
        $this->db->query('SELECT id, text_code FROM t_user_user_status');
        $res = $this->db->resultSet();
        $statuses = [];
        if ($res) {
            foreach ($res as $s) {
                $statusId = is_object($s) ? $s->id : $s['id'];
                $textCode = is_object($s) ? $s->text_code : $s['text_code'];
                $statuses[$statusId] = $textCode;
            }
        }
        return $statuses;
    }

    // Récupérer les rôles d'un utilisateur (seulement les IDs)
    public function getUserRoles($userId)
    {
        $this->db->query('SELECT role FROM t_user_user_role WHERE user_id = :id');
        $this->db->bind(':id', $userId);
        $roles = $this->db->resultSet();
        $roleArray = [];
        if ($roles) {
            foreach ($roles as $r) {
                $roleArray[] = is_object($r) ? $r->role : $r['role'];
            }
        }
        return $roleArray;
    }

    // Sauvegarder les rôles d'un utilisateur
    public function saveUserRoles($userId, $roles)
    {
        $this->db->query('DELETE FROM t_user_user_role WHERE user_id = :id');
        $this->db->bind(':id', $userId);
        $this->db->execute();

        foreach ($roles as $role) {
            $this->db->query('INSERT INTO t_user_user_role (user_id, role) VALUES (:id, :role)');
            $this->db->bind(':id', $userId);
            $this->db->bind(':role', $role);
            $this->db->execute();
        }
    }

    // Trouver un utilisateur par email ou username
    public function findUserByEmailOrUsername($identifier)
    {
        $this->db->query('SELECT * FROM t_user_user WHERE email = :identifier OR username = :identifier');
        $this->db->bind(':identifier', $identifier);

        $row = $this->db->single();

        if ($this->db->rowCount() > 0) {
            if (is_array($row)) {
                $row['roles'] = $this->getUserRoles($row['id']);
            } elseif (is_object($row)) {
                $row->roles = $this->getUserRoles($row->id);
            }
            return $row;
        } else {
            return false;
        }
    }

    // Récupérer un utilisateur par ID
    public function getUserById($id)
    {
        $this->db->query('
            SELECT u.*, creator.username as created_by_name, modifier.username as modified_by_name 
            FROM t_user_user u 
            LEFT JOIN t_user_user creator ON u.created_by = creator.id 
            LEFT JOIN t_user_user modifier ON u.modified_by = modifier.id 
            WHERE u.id = :id
        ');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        if ($this->db->rowCount() > 0) {
            if (is_array($row)) {
                $row['roles'] = $this->getUserRoles($id);
            } else {
                $row->roles = $this->getUserRoles($id);
            }
        }
        return $row;
    }

    // Récupérer tous les utilisateurs
    public function getUsers()
    {
        $this->db->query('SELECT id, username, email, created_at, status_id FROM t_user_user ORDER BY created_at DESC');
        $users = $this->db->resultSet();
        foreach ($users as &$user) {
            if (is_array($user)) {
                $user['roles'] = $this->getUserRoles($user['id']);
            } else {
                $user->roles = $this->getUserRoles($user->id);
            }
        }
        return $users;
    }

    // Ajouter un utilisateur
    public function addUser($data)
    {
        $this->db->query('INSERT INTO t_user_user (username, email, password_hash, status_id) VALUES (:username, :email, :password, :status_id)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':status_id', $data['status_id'] ?? 2);

        try {
            if ($this->db->execute()) {
                // To get the inserted ID, some PDOs support lastInsertId, but Postgres requires RETURNING id or currval
                // Let's just find the user to get ID safely.
                $user = $this->findUserByEmailOrUsername($data['email']);
                if ($user) {
                    $userId = is_array($user) ? $user['id'] : $user->id;
                    $this->saveUserRoles($userId, $data['roles'] ?? ['user']);
                }
                return true;
            }
        } catch (PDOException $e) {
            return false;
        }
        return false;
    }

    // Mettre à jour utilisateur
    public function updateUser($data)
    {
        if (!empty($data['password'])) {
            $this->db->query('UPDATE t_user_user SET username = :username, email = :email, password_hash = :password, status_id = :status_id WHERE id = :id');
            $this->db->bind(':password', $data['password']);
        } else {
            $this->db->query('UPDATE t_user_user SET username = :username, email = :email, status_id = :status_id WHERE id = :id');
        }

        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':status_id', $data['status_id'] ?? 2);
        $this->db->bind(':id', $data['id']);

        if ($this->db->execute()) {
            $this->saveUserRoles($data['id'], $data['roles'] ?? ['user']);
            return true;
        }
        return false;
    }

    // Enregistrement par défaut (user normal)
    public function register($data)
    {
        $this->db->query('INSERT INTO t_user_user (username, email, password_hash) VALUES (:username, :email, :password)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        try {
            if ($this->db->execute()) {
                $user = $this->findUserByEmailOrUsername($data['email']);
                if ($user) {
                    $userId = is_array($user) ? $user['id'] : $user->id;
                    $this->saveUserRoles($userId, ['user']);
                }
                return true;
            }
        } catch (PDOException $e) {
            return false;
        }
        return false;
    }

    // Supprimer un utilisateur
    public function deleteUser($id)
    {
        // La contrainte FOREIGN KEY ON DELETE CASCADE gère les rôles
        $this->db->query('DELETE FROM t_user_user WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
