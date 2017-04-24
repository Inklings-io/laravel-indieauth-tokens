<?php
namespace Inklings\IndieAuthTokens;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class TokenController extends Controller
{
 
    public function index(Request $request)
    {
        if (isset($request['code']) &&
            isset($request['me']) &&
            isset($request['state']) &&
            isset($request['redirect_uri'])) {

            $post_data = http_build_query(array(
                'code'          => $request['code'],
                'me'            => $request['me'],
                'redirect_uri'  => $request['redirect_uri'],
                'client_id'     => $request['client_id'],
                'state'         => $request['state']
            ));

            $auth_endpoint = IndieAuth\Client::discoverAuthorizationEndpoint($request['me']);

            $ch = curl_init($auth_endpoint);

            if (!$ch) {
                $this->log->write('error with curl_init');
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

            $response = curl_exec($ch);

            $results = array();
            parse_str($response, $results);

            if ($results['me']) {

                $user = $results['me'];
                $scope = $results['scope'];
                $client_id = $this->request->post['client_id'];

                $token = 'asdfasdfasdfasdf';
                //TODO
                //$this->load->model('auth/token');
                //$token = $this->model_auth_token->newToken($user, $scope, $client_id);
                //TODO

                $this->response->setOutput(http_build_query(array(
                    'access_token' => $token,
                    'scope' => $scope,
                    'me' => $user)));
            } else {
                header('HTTP/1.1 400 Bad Request');
                exit();
            }
        } else {
            header('HTTP/1.1 400 Bad Request');
            exit();
        }
    }
 
}
