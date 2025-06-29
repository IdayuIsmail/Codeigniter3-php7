<?php
defined('BASEPATH') or exit('No direct script access allowed');

use League\OAuth2\Client\Provider\GenericProvider;

class Oauth extends CI_Controller
{
    public $data;
    private $provider;

    public function __construct()
    {
        parent::__construct();
        $this->data['base_url'] = base_url();
        $this->load->helper('form');
        $this->data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        $this->provider = new GenericProvider([
            'clientId'                => $this->config->item('keycloak_client_id'),
            'clientSecret'            => $this->config->item('keycloak_client_secret'),
            'redirectUri'             => $this->config->item('keycloak_redirect_uri'),
            'urlAuthorize'            => $this->config->item('keycloak_base_url') . '/realms/' . $this->config->item('keycloak_realm') . '/protocol/openid-connect/auth',
            'urlAccessToken'          => $this->config->item('keycloak_base_url') . '/realms/' . $this->config->item('keycloak_realm') . '/protocol/openid-connect/token',
            'urlResourceOwnerDetails' => $this->config->item('keycloak_base_url') . '/realms/' . $this->config->item('keycloak_realm') . '/protocol/openid-connect/userinfo',
        ]);
    }

    public function login()
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'scope' => 'openid profile email'
        ]);
        $this->session->set_userdata('oauth2state', $this->provider->getState());
        redirect($authorizationUrl);
    }

    public function callback()
    {
        if (empty($this->input->get('state')) || ($this->input->get('state') !== $this->session->userdata('oauth2state'))) {
            $this->session->unset_userdata('oauth2state');
            show_error('Invalid state');
        }

        try {
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $this->input->get('code'),
            ]);
            $this->session->set_userdata('id_token', $accessToken->getValues()['id_token']);
            $resourceOwner = $this->provider->getResourceOwner($accessToken);
            $userData = $resourceOwner->toArray();
            $nric = $userData['nric'];  
            $name = $userData['nama'];  
            $hashed_nric = hash('sha256', $nric); 

            // agency business logic - lookup nric in the database
            $this->db->where('ic_number', $hashed_nric);
            $user = $this->db->get('administrators')->row_array();

            if ($user) {
                $this->session->set_userdata('keycloak_user', $userData);
                redirect('dashboard');
            } else {
                // agency business logic
                $this->session->set_userdata('keycloak_temp_data', $userData);  
                redirect('oauth/show_modal_options'); // Redirect to prevent reload issues
            }
        } catch (Exception $e) {
            show_error('Failed to authenticate with Keycloak: ' . $e->getMessage());
        }
    }

	public function logout()
{
	$id_token = $this->session->userdata('id_token');
    $baseurl = $this->config->item('keycloak_base_url');
    $realm = $this->config->item('keycloak_realm');
    $client_id = $this->config->item('keycloak_client_id');

    $this->session->unset_userdata('keycloak_user');
	$this->session->set_flashdata('success_message', 'Logged out successfully');

        $logoutUrl = $baseurl . '/realms/' . $realm . '/protocol/openid-connect/logout?' . http_build_query([
            'id_token_hint' => $id_token,
            'post_logout_redirect_uri' => base_url('admin'),
			'client_id' => $client_id,
        ]);
        redirect($logoutUrl);
}
}
