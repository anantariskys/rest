<?php
// RateLimiter.php
class RateLimiter {
    private $storageDir;
    private $maxRequests;
    private $timeWindow;

    public function __construct($maxRequests = 5, $timeWindow = 10) {
        $this->maxRequests = $maxRequests; 
        $this->timeWindow = $timeWindow;   
        $this->storageDir = __DIR__ . '/rate_limits';


        if (!file_exists($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
    }

    private function getFilePath($userId) {
      
    $userId = preg_replace('/[^a-zA-Z0-9_]/', '_', $userId); 
    return $this->storageDir . '/user_' . $userId . '.json';
    }

    private function getCurrentData($userId) {
        $filePath = $this->getFilePath($userId);
        if (!file_exists($filePath)) {
            return null;
        }

        $data = json_decode(file_get_contents($filePath), true);

       
        if ($data['timestamp'] < (time() - $this->timeWindow)) {
            unlink($filePath);
            return null;
        }

        return $data;
    }

   
    public function checkLimit($userId) {
        $data = $this->getCurrentData($userId);

        if (!$data) {
        
            $data = [
                'count' => 1,
                'timestamp' => time()
            ];
            file_put_contents($this->getFilePath($userId), json_encode($data));
            return true;
        }

        if ($data['count'] >= $this->maxRequests) {
            return false;
        }

      
        $data['count']++;
        file_put_contents($this->getFilePath($userId), json_encode($data));
        return true;
    }

   
    public function cleanup() {
        $files = glob($this->storageDir . '/*.json');
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if ($data['timestamp'] < (time() - $this->timeWindow)) {
                unlink($file);
            }
        }
    }
}
