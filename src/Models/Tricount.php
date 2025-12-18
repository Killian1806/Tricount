<?php

namespace Models;

use PDO;

class Tricount extends Database
{

    public function create($title, $currency, $userId)
    {
        try {
            $this->db->beginTransaction();

            // Insertion du groupe
            $query = $this->db->prepare("INSERT INTO tricounts (title, money, creation) VALUES (:title, :money, NOW())");
            $query->execute([
                'title' => htmlspecialchars($title),
                'money' => htmlspecialchars($currency)
            ]);

            $tricountId = $this->db->lastInsertId();

            // On ajoute l'utilisateur actuel comme participant 
            $queryPart = $this->db->prepare("INSERT INTO participants (tricount_id, user_id, alias_name, balance) VALUES (?, ?, ?, 0)");
            $queryPart->execute([$tricountId, $userId, 'Moi']);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            // Affiche l'erreur SQL précise
            die("Erreur SQL : " . $e->getMessage());
            return false;
        }
    }

    public function getUserTricounts($userId)
    {
        $query = $this->db->prepare("
            SELECT t.* FROM tricounts t
            INNER JOIN participants p ON t.id = p.tricount_id
            WHERE p.user_id = :userId
            ORDER BY t.id DESC
        ");
        $query->execute(['userId' => $userId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM tricounts WHERE id = :id");
        return $query->execute(['id' => $id]);
    }
}
