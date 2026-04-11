<?php

declare(strict_types=1);
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Model
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(string $table, object $item): ?int
    {
        $data = (array)$item;
        // Protegemos las columnas con comillas dobles para compatibilidad ANSI
        $keys = '"' . implode('", "', array_keys($data)) . '"';
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO \"{$table}\" ({$keys}) VALUES ({$placeholders})";
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($data as $key => $value) { 
                $stmt->bindValue(':' . $key, $value); 
            }
            $stmt->execute();
            $id = (int)$this->pdo->lastInsertId();
            $this->log($sql);
            return $id;
        } catch (PDOException $e) {
            error_log("Error al guardar en '{$table}': " . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $table, object $item, int $id): ?int
    {
        $data = (array)$item;
        $setClauses = [];
        foreach ($data as $key => $value) { 
            $setClauses[] = "\"{$key}\" = :{$key}"; 
        }
        $setSql = implode(', ', $setClauses);
        $sql = "UPDATE \"{$table}\" SET {$setSql} WHERE \"id\" = :id";
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($data as $key => $value) { 
                $stmt->bindValue(':' . $key, $value); 
            }
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $this->log($sql);
            return $id;
        } catch (PDOException $e) {
            error_log("Error al actualizar en '{$table}' (ID: {$id}): " . $e->getMessage());
            throw $e;
        }
    }

    public function updateAll(string $table, object $item, array $ids): bool
    {
        $cleanIds = array_map('intval', $ids);
        if (empty($cleanIds)) return false;
        $setClauses = [];
        $data = (array)$item;
        foreach ($data as $key => $value) { 
            $setClauses[] = "\"{$key}\" = :{$key}"; 
        }
        $setSql = implode(', ', $setClauses);
        $idPlaceholders = [];
        foreach ($cleanIds as $index => $id) { 
            $idPlaceholders[] = ":id_{$index}"; 
        }
        $inClause = implode(', ', $idPlaceholders);
        $sql = "UPDATE \"{$table}\" SET {$setSql} WHERE \"id\" IN ({$inClause})";
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($data as $key => $value) { 
                $stmt->bindValue(':' . $key, $value); 
            }
            foreach ($cleanIds as $index => $id) { 
                $stmt->bindValue(":id_{$index}", $id, PDO::PARAM_INT); 
            }
            $stmt->execute();
            $this->log($sql);
            return (bool)$stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error al actualizar múltiples en '{$table}': " . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $table, string $whereClause, array $params = []): int
    {
        $sql = "DELETE FROM \"{$table}\" WHERE {$whereClause}";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $this->log($sql);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error al eliminar de '{$table}' ({$whereClause}): " . $e->getMessage());
            throw $e;
        }
    }

    public function list($fields, $table, $filters = '', $joins = '') 
    {
        try {
            $stm = $this->pdo->prepare("SELECT $fields FROM $table $joins WHERE 1=1 $filters");
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) { 
            die($e->getMessage()); 
        }
    }

    public function get($fields, $table, $filters = '', $joins = '') 
    {
        try {
            $sql = "SELECT $fields FROM $table $joins WHERE 1=1 $filters";
            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) { 
            die($e->getMessage()); 
        }
    }

    public function getToken(int $length): string { return bin2hex(random_bytes($length)); }

    public function clearAuthCookie(): void
    {
        $past = time() - 3600;
        setcookie("user_login", "", $past, "/");
        setcookie("random_password", "", $past, "/");
        setcookie("random_selector", "", $past, "/");
    }

    public function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        return $_SERVER['REMOTE_ADDR'];
    }

    public function getUserDevice() { return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'; }

    public function log($query)
    {
        $userId = $_SESSION["id-SIGMA"] ?? 0;
        $query = trim(preg_replace("/\s+/", " ", $query));
        $ip = $this->getUserIP();
        $device = $this->getUserDevice();
        try {
            // Placeholder real para evitar errores de escape y tipos
            $sql = 'INSERT INTO "log" ("kind", "query", "user_id", "ip", "device") VALUES (?, ?, ?, ?, ?)';
            $this->pdo->prepare($sql)->execute(['query', $query, $userId, $ip, $device]);
        } catch (Exception $e) { 
            // Silencioso
        }
    }

    public function updateblob(string $table, array $data, string $where): bool
    {
        $setClauses = [];
        foreach ($data as $key => $value) { 
            $setClauses[] = "\"{$key}\" = :{$key}"; 
        }
        $setSql = implode(', ', $setClauses);
        $sql = "UPDATE \"{$table}\" SET {$setSql} WHERE {$where}";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (Exception $e) { 
            die($e->getMessage()); 
        }
    }

    public function sendEmail($item)
    {
        date_default_timezone_set('America/Bogota');
        $dotenv = Dotenv\Dotenv::createUnsafeImmutable('/var/www/html/sigma/');
        $dotenv->load();
        $mail = new PHPMailer(true);
        $mail->CharSet    = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = "smtp.office365.com";
        $mail->Port       = 587;
        $mail->SMTPSecure = "starttls";
        $mail->SMTPAuth   = true;
        $mail->Username   = "sigmareport@es-metals.com";
        $mail->Password   = getenv('EMAILPASS');
        $mail->setFrom("sigmareport@es-metals.com");
        
        foreach ($item->to as $r) { $mail->addAddress($r); }
        $mail->addReplyTo($item->email, 'EsMetals');

        $logoPath = "/var/www/html/sigma/app/assets/img/logoES.png";
        if (file_exists($logoPath)) {
            $mail->AddEmbeddedImage($logoPath, 'scope');
        }

        if (isset($item->maps) && is_array($item->maps)) {
            foreach ($item->maps as $cid => $path) {
                if (file_exists($path)) {
                    $mail->AddEmbeddedImage($path, $cid);
                }
            }
        }

        $mail->Subject = "$item->subject";
        $msg = "<div style='font-family:Century Gothic;'>$item->body</div>";
        $mail->msgHTML($msg);

        if (!empty($item->attachments)) {
            foreach ($item->attachments as $file) {
                if (file_exists($file)) $mail->addAttachment($file);
            }
        }

        if (!empty($item->icsFile) && file_exists($item->icsFile)) {
            $mail->addStringAttachment(
                file_get_contents($item->icsFile),
                'invite.ics',
                'base64',
                'text/calendar; method=REQUEST'
            );
        }

        if (!$mail->send()) return "Email Failed";
        return json_encode($item->to);
    }

    public function saveblob($table, $item)
    {
        $data = (array)$item;
        $keys = '"' . implode('", "', array_keys($data)) . '"';
        $placeholders = ":" . implode(",:", array_keys($data));
        try {
            $stmt = $this->pdo->prepare("INSERT INTO \"$table\" ($keys) VALUES ($placeholders)");
            $stmt->execute($data);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) { 
            die($e->getMessage()); 
        }
    }
}