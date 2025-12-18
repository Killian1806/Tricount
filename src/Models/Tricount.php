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

    public function getById($id)
    {
        $query = $this->db->prepare("SELECT * FROM tricounts WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

public function getExpensesByGroup($groupId) {
    // Jointure entre depenses et participants pour avoir le nom de celui qui a payé
    $query = $this->db->prepare("
        SELECT d.*, p.alias_name as payer_name 
        FROM depenses d
        JOIN participants p ON d.payer_id = p.id
        WHERE d.tricount_id = :groupId
        ORDER BY d.date DESC
    ");
    $query->execute(['groupId' => $groupId]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

public function getParticipants($groupId) {
    $query = $this->db->prepare("SELECT * FROM participants WHERE tricount_id = :groupId");
    $query->execute(['groupId' => $groupId]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

public function addExpense($groupId, $description, $amount, $category, $payerId) {
    try {
        $query = $this->db->prepare("
            INSERT INTO depenses (tricount_id, amount, payer_id, description, category, type, date) 
            VALUES (:groupId, :amount, :payerId, :description, :category, 'expense', NOW())
        ");
        return $query->execute([
            'groupId'     => $groupId,
            'amount'      => $amount,
            'payerId'     => $payerId,
            'description' => htmlspecialchars($description),
            'category'    => $category
        ]);
    } catch (\Exception $e) {
        return false;
    }
}
}