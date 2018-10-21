<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '/libraries/REST_Controller.php');

use Restserver\libraries\REST_Controller;


class Login extends REST_Controller
{


	public function __construct()
	{

		header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");


		parent::__construct();
		$this->load->database();

	}

	public function index_get()
	{
		$this->response('Hola',REST_Controller::HTTP_BAD_REQUEST);
	}

	public function index_post()
	{

		$data = $this->post();

		if (!isset($data['email']) OR !isset($data['password']) OR !isset($data['refId']) OR !isset($data['token'])) {

			$respuesta = array(
				'error' => TRUE,
				'mensaje' => 'La información enviada no es válida'
			);
			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}

		// Tenemos correo y contraseña en un post
		$dataEmail = array(
			'email' => $data['email'],
			'password' => $data['password'],
		);

		$queryEmail = $this->db->get_where('usuarios', $dataEmail);
		$usuario = $queryEmail->row();

		if (!isset($usuario)) {
			$respuesta = array(
				'error' => TRUE,
				'mensaje' => 'Usuario y/o contrasena no son váidos'
			);
			$this->response($respuesta);
			return;
		}

		// AQUI!, tenemos un usuario y contraseña

		// TOKEN
		// $token = bin2hex( openssl_random_pseudo_bytes(20)  );
		$token = hash('n0t1f1c4c10n3s', $data['email']);

		// Guardar en base de datos el token
		$this->db->reset_query();
		$updateToken = array('token' => $token);
		$this->db->where('first_name', $usuario->first_name);
		$this->db->where('last_name', $usuario->last_name);
		$this->db->where('email', $usuario->email);
		$this->db->where('refId', $usuario->refId);

		$hecho = $this->db->update('usuarios', $updateToken);

		$respuesta = array(
			'error' => FALSE,
			'email' => $usuario->email,
			'first_name' => $usuario->first_name,
			'last_name' => $usuario->last_name,
			'token' => $token,
			'refId' => $usuario->refId
		);


		$this->response($respuesta);


	}


}
