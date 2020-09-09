<?php


namespace App\Helpers;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\String_;

class DocHelper
{
    const structure = [
        "tags" => [],
        "requests" => []
    ];
    private $request_model = [
        "tag" => "",
        "description" => "",
        "method" => "",
        "uri" => "",
        "request_headers" => [],
        "request_body" => [],
        "status"=> 0,
        "response_body" => []
    ];
    private $re = '/(?<=[a-z])(?=[A-Z])/x';
    private $data = [];
    private $file = [];

    public static function init()
    {
        Storage::disk('public')->put('docs.json', json_encode(DocHelper::structure));
    }

    public static function make($name, $request, $response){
        return new self($name, $request, $response);
    }

    private function __construct($name, $request, $response)
    {
        $this->resolveName($name);
        $this->makeRequestStructure($request, $response);
        $this->updateFile();
    }

    private function resolveName($name)
    {
        $this->file = json_decode(Storage::get('public/docs.json'), true);
        $result = preg_split($this->re, $name);
        $tags = $this->file["tags"];
        array_push($tags, $result[1]);
        $this->file["tags"] = array_unique($tags);
        $this->data = $result;
    }
    private function makeRequestStructure($request, $response){
        $this->request_model["tag"] = $this->data[1];
        $this->request_model["description"] = implode(" ",array_slice($this->data, 1));
        $this->request_model["method"] = $request["method"];
        $this->request_model["uri"] = $request["uri"];
        $this->request_model["request_headers"] = $request["headers"];
        $this->request_model["request_body"] = $request["body"];
        $this->request_model["status"] = $response->status();
        $this->request_model["response_body"] = $response->getOriginalContent();
    }

    private function updateFile()
    {
        $r = array_walk_recursive($this->request_model, function (&$x){
            if(gettype($x) == "object"){
                $x = "[Object]";
            }
        });
        $requests = $this->file["requests"];
        array_push($requests, $this->request_model);
        $this->file["requests"] = $requests;
        $this->file = json_encode($this->file);
        Storage::disk('public')->put('docs.json', $this->file);
    }
}
