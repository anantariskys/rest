<?php
include_once '../config/db.php';
require_once '../config/variable.php';
require_once 'index.php';

function getUserById($id): array
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        unset($user['password']);
        return sendResponse(200, 'User found', $user);
    }
    return sendResponse(404, 'User not found');
}

function updateUser($id, $data): array
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return sendResponse(404, 'User not found');
    }

    $fields = [];
    $values = [];

    $username = $data->username ?? $user['username'];
    if ($username !== $user['username']) {
        $fields[] = "username = ?";
        $values[] = $username;
    }

    $max_reservations = $data->max_reservations ?? $user['max_reservations'];
    if ($max_reservations !== $user['max_reservations']) {
        $fields[] = "max_reservations = ?";
        $values[] = $max_reservations;
    }

    $membership_status = $data->membership_status ?? $user['membership_status'];
    if ($membership_status !== $user['membership_status']) {
        $fields[] = "membership_status = ?";
        $values[] = $membership_status;
    }

    $role = $data->role ?? $user['role'];
    if ($role !== $user['role']) {
        $fields[] = "role = ?";
        $values[] = $role;
    }

    if (count($fields) > 0) {
        $values[] = $id;
        $sql = "UPDATE members SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        return sendResponse(200, 'User updated successfully', [
            'id' => $id,
            'name' => $username,
            'max_reservations' => $max_reservations,
            'membership_status' => $membership_status,
            'role' => $role
        ]);
    } else {
        return sendResponse(400, 'No valid fields to update');
    }
}