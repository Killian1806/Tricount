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

/**
 * Ajoute un participant à un groupe
 */
public function addParticipant($tricountId, $name, $email = null) {
    try {
        // On force l'affichage des erreurs pour ce test
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = $this->db->prepare("
            INSERT INTO participants (tricount_id, user_id, alias_name, balance, email) 
            VALUES (:tricountId, NULL, :alias_name, 0, :email)
        ");
        
        return $query->execute([
            'tricountId' => $tricountId,
            'alias_name' => htmlspecialchars($name),
            'email'      => $email
        ]);

    } catch (\PDOException $e) {
        // SI CA ECHO ICI, VOUS VERREZ L'ERREUR SUR VOTRE PAGE GROUPE.PHP
        echo "<div style='background:red; color:white; padding:20px; position:fixed; top:0; z-index:9999;'>";
        echo "<h3>Erreur SQL détectée :</h3>";
        echo $e->getMessage();
        echo "</div>";
        return false;
    }
}

/**
 * Supprime un participant d'un groupe
 */
public function removeParticipant($participantId, $tricountId) {
    try {
        // Vérifier que le participant n'a pas de dépenses
        $checkQuery = $this->db->prepare("
            SELECT COUNT(*) as count FROM depenses 
            WHERE payer_id = :participantId AND tricount_id = :tricountId
        ");
        $checkQuery->execute([
            'participantId' => $participantId,
            'tricountId' => $tricountId
        ]);
        $result = $checkQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            // Le participant a des dépenses, on ne peut pas le supprimer
            return false;
        }
        
        // Supprimer le participant
        $deleteQuery = $this->db->prepare("
            DELETE FROM participants 
            WHERE id = :participantId AND tricount_id = :tricountId
        ");
        return $deleteQuery->execute([
            'participantId' => $participantId,
            'tricountId' => $tricountId
        ]);
    } catch (\Exception $e) {
        return false;
    }
}

}