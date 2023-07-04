<?php

namespace App\Controller;

use Error;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RequestContext;

class todoController extends AbstractController{

    #[Route("/")]
    public function works()
    {
        return $this->render("Tareas/file.html.twig");
    }

    // #[Route("/tareas")]
    // public function getTareas()
    // {
    //     $archivo = file_get_contents("tareas.json");
    //     $data = json_decode($archivo);

      

    //     // return new Response($archivo,200,["Content-Type"]);
    //      return $this->render("Tareas/file.html.twig",["tareas"=>$data]);
    // }
    #[Route("/tareas")]
    public function getTareas()
    {
        $archivo = file_get_contents("tareas.json");
        $data = json_decode($archivo);

        $response = new JsonResponse(["tareas"=>$data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
        //return new JsonResponse(["tareas"=>$data],200,['Access-Control-Allow-Origin','*']);
        // return new Response($archivo,200,["Content-Type"]);
         //return $this->render("Tareas/file.html.twig",["tareas"=>$data]);
    }



    #[Route("/tarea/{id}", methods: ['GET'])]
    public function getTareaById($id)
    {
        $archivo=file_get_contents("tareas.json");
        $data = json_decode($archivo);
        $tarea=null;

        foreach($data as $value)
        {
            //echo "id es $id y identificador es $value->identificador";
            if ($value->identificador == $id){
               $tarea=$value;
               break;
            }
        }
        if ($tarea){
            $response = new JsonResponse($tarea);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else{
            return new Response("La tarea no existe",404);
        }


        
        // return new JsonResponse($tarea);
         //return $this->render("Tareas/tarea.html.twig",["tarea"=>$tarea]);
    }

    #[Route("/tarea/{id}",'delete', methods: ['DELETE'])]
    public function deleteTareaById($id)
    {
        $archivo=file_get_contents("tareas.json");
        $data = json_decode($archivo);
        $tarea=null;
       //dd($data);
        foreach($data as $key=>$value)
        {
            //echo "id es $id y identificador es $value->identificador";
            if ($value->identificador == $id){
               $tarea=$value;
               unset($data[$key]);
              //dd($data[$key]);
               break;
            }
            
        }

        if ($tarea){
            file_put_contents("tareas.json",json_encode($data,JSON_PRETTY_PRINT));
            $response = new JsonResponse(["tareas"=>$tarea]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
        else{
            return new Response("La tarea no existe",404);
        }


        
        // return new JsonResponse($tarea);
         //return $this->render("Tareas/tarea.html.twig",["tarea"=>$tarea]);
    }

    #[Route("/tarea","POST")]
    public function addTarea()
    {
        $request=new Request();
        $datos=json_decode($request->getContent());

       // dd($datos->nombre); 
        
        $descripcion=$datos->descripcion;
        $nombre=$datos->nombre;

        $archivo=file_get_contents("tareas.json");
        //var_dump($archivo);
        $data = json_decode($archivo);
       // var_dump($data);
        $lastId=0;
        foreach($data as $value)
        {
            if($value->identificador > $lastId)
            {
                $lastId = $value->identificador;
            }
        }
        $lastId++;
        

        $newTarea=[
            "nombre"=>$nombre,
            "descripcion"=>$descripcion,
            "estado"=>"sin hacer",
            "identificador"=>$lastId

        ];
        array_push($data,$newTarea);
        
        //var_dump($data);
        
        $newJson=file_put_contents("tareas.json",json_encode($data,JSON_PRETTY_PRINT));

        if ($newJson){
            $response = new JsonResponse(["nueva"=>$newTarea],200);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Content-Type","application/json");
            $response->headers->set("Accept","*");
            return $response;
            
        }
        else{
            return new Response("Ha ocurrido un error", 500);
        }
        

    }

    #[Route("/tarea/{id}", methods: ['PUT'])]
    public function editTarea($id)
    {
        $request=new Request();
        $datos=json_decode($request->getContent());

       // dd($datos->nombre); 
        
        $descripcion=$datos->descripcion;
        $nombre=$datos->nombre;

        $archivo=file_get_contents("tareas.json");
        //var_dump($archivo);
        $data = json_decode($archivo);
        $tarea=null;
        foreach($data as $value)
        {
            if($value->identificador == $id)
            {
                $value->nombre = $nombre;
                $value->descripcion = $descripcion;
                $tarea=$value;
                break;
            }
        }
        //dd($data);
        $modified=file_put_contents("tareas.json",json_encode($data,JSON_PRETTY_PRINT));
        if ($modified){
            $response = new JsonResponse(["modificada"=>$tarea],200);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set("Content-Type","application/json");
            $response->headers->set("Accept","*");
            return $response;
            
        }
        else{
            return new Response("Ha ocurrido un error", 500);
        }

    }

    #[Route("/tarea/{id}/done", methods: ['PUT'])]
    public function tareaDone($id)
    {
        $archivo=file_get_contents("tareas.json");
        $data = json_decode($archivo);
        

        foreach($data as $value)
        {
            //echo "id es $id y identificador es $value->identificador";
            if ($value->identificador == $id){
               $value->estado="hecha";
               break;
            }
        }

        $modified=file_put_contents("tareas.json",json_encode($data,JSON_PRETTY_PRINT));
        if ($modified){
            return new Response("Tarea modificada a estado Done", 200);
            
        }
        else{
            return new Response("Ha ocurrido un error", 500);
        }


        
        // return new JsonResponse($tarea);
         //return $this->render("Tareas/tarea.html.twig",["tarea"=>$tarea]);
    }

    #[Route("/tarea/{id}/undone", methods: ['GET'])]
    public function tareaUnDone($id)
    {
        $archivo=file_get_contents("tareas.json");
        $data = json_decode($archivo);
        

        foreach($data as $value)
        {
            //echo "id es $id y identificador es $value->identificador";
            if ($value->identificador == $id){
               $value->estado="sin hacer";
               break;
            }
        }

        $modified=file_put_contents("tareas.json",json_encode($data,JSON_PRETTY_PRINT));
        if ($modified){
            return new Response("Tarea modificada a estado Done", 200);
            
        }
        else{
            return new Response("Ha ocurrido un error", 500);
        }


        
        // return new JsonResponse($tarea);
         //return $this->render("Tareas/tarea.html.twig",["tarea"=>$tarea]);
    }



    
}