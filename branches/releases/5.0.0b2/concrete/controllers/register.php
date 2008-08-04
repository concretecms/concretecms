<?

class RegisterController extends Controller {

	public function view() {
		$u = new User();
		$this->set('u', $u);
	}

}

?>