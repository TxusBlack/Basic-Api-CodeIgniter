<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '/libraries/REST_Controller.php');

use Restserver\libraries\REST_Controller;


class Registro extends REST_Controller
{

	public function __construct()
	{
		header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");

		parent::__construct();
		$this->load->database();
	}

	public function index_post()
	{

		$data = $this->post();

		if (!isset($data['first_name']) OR !isset($data['last_name']) OR !isset($data['email']) OR !isset($data['password']) OR !isset($data['role'])) {
			$res = array(
				'error' => TRUE,
				'mensaje' => 'La información enviada no es válida'
			);
			$this->response($res, REST_Controller::HTTP_BAD_REQUEST);
			return;
		} else {
			$email_parts = explode('@', $data['email'], 2);
			$user = trim($email_parts[0]);
			$domain = trim($email_parts[1]);

			if ($domain === 'unisangil.edu.co') {
				$res = array(
					'error' => TRUE,
					'mensaje' => 'El correo no pertenece a UNISANGIL'
				);
				$this->response($res, REST_Controller::HTTP_UNAUTHORIZED);
				return;
			} else {
				// Get Users
				$getUser = array('email' => $data['email']);
				$query = $this->db->get_where('usuarios', $getUser);
				$user = $query->row();

				if (isset($user)) {
					$res = array(
						'error' => TRUE,
						'mensaje' => 'El usuario ya se encuentra registrado'
					);
					$this->response($res, REST_Controller::HTTP_BAD_REQUEST);
					return;
				} else {
					$refId = hash('n0t1f1c4c10n3s', $data['password']);
					$token = hash('n0t1f1c4c10n3s', $data['email']);
					$dataRes = array(
						'first_name' => $data['first_name'],
						'last_name' => $data['last_name'],
						'email' => $data['email'],
						'password' => $data['password'],
						'role' => $data['role'],
						'refId' => $refId,
						'token' => $token
					);

					// Create the user
					$this->db->reset_query();
					$this->db->insert('usuarios', $dataRes);

					$res = array(
						'error' => FALSE,
						'refId' => $refId,
						'token' => $token
					);

					$this->response($res);
				}
			}
		}
	}
}
