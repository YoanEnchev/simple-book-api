<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // Fields which data is ok to be shown to anyone publicly.
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_author' => $this->is_author
        ];

        $route = $request->route()->getName();
        
        // On api login or register.
        if($route === 'api.register' || $route === 'api.login') {
            // Data that must be accessed only by the authenticated user.
            $result['api_token'] = $this->api_token;
        }

        return $result;
    }
}
