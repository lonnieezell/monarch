<?php

namespace Tests\Support\Routes;

use Monarch\Routes\API;

return new class () extends API {
    public function get()
    {
        return $this->respond([
            'message' => 'GET request successful'
        ]);
    }

    public function post()
    {
        return $this->respond([
            'message' => 'POST request successful'
        ]);
    }

    public function put()
    {
        return $this->respond([
            'message' => 'PUT request successful'
        ]);
    }

    public function delete()
    {
        return $this->respond([
            'message' => 'DELETE request successful'
        ]);
    }
};
