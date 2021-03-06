<?php

namespace app\controllers;

use row\core\Options;
use app\specs\Controller;
use app\specs\ControllerACL;
use row\http\NotFoundException;
use app\models;
use row\utils\Inflector;
use row\auth\Session;

class blogController extends Controller {

	static public $config = array(
		'posts_on_index' => 3,
	);

	protected function _init() {
		parent::_init();

		// This Controller uses "Action" as postfix for its Actions
		$this->_dispatcher->options->action_name_postfix = 'Action';

		$this->acl->add('true'); // adds required zone "true" to all Actions of this Controller
		$this->acl->add('logged in', array('add_post', 'edit_post', 'edit_comment', 'publish_post', 'unpublish_post'));
		$this->acl->add('blog create posts', 'add_post');
	}

	public function userAction( $id ) {
		try {
			$user = models\User::get($id);
		}
		catch ( \Exception $ex ) {
			throw new NotFoundException('User # '.$id);
		}

		return $this->tpl->display(__METHOD__, get_defined_vars(), false); // Show only View, no Layout
	}

	// A Action port to the publish function that does practically the same
	public function unpublish_postAction( $post ) {
		return $this->publish_post($post, 0);
	}

	// (un)Publish a post IF you have the right access
	public function publish_postAction( $post, $publish = null ) {
		$post = $this->getPost($post);
		is_int($publish) or $publish = 1;
		if ( $this->user->hasAccess('blog '.( $publish ? '' : 'un' ).'publish') ) {
			$post->doPublish($publish);
		}
		$this->redirect($post->url());
	}

	// Show 1 category. Most 'logic' in the Category Model
	public function categoryAction( $category ) {
		$category = models\Category::get($category);

		$messages = Session::messages();
		return $this->tpl->display(__METHOD__, get_defined_vars());
	}

	// Show Categories list OR an alias for the category Action
	// Using a little SQL is fine, because it's valid for all SQLAdapters
	public function categoriesAction( $category = null ) {
		if ( null !== $category ) {
			return $this->category($category);
		}

		$categories = models\Category::all('1 ORDER BY category_name ASC');

		$messages = Session::messages();
		return $this->tpl->display(__METHOD__, get_defined_vars());
	}

	// The Add post form and the submit logic
	// Form is (manually) 'built' in the template
	// Validation from the Post Model
	public function add_postAction() {
		$validator = models\Post::validator('add');
		if ( !empty($_POST) ) {
			if ( $validator->validate($_POST) ) {
				$insert = $validator->output;
				$insert['author_id'] = $this->user->UserID();
				$insert['created_on'] = time();
				if ( $pid = models\Post::insert($insert) ) {
					$post = models\Post::get($pid);
					Session::success('Post Created. Look:');
					$this->redirect($post->url());
				}
				Session::error('Couldn\'t save... =( Try again!?');
			}
		}

//		$post = Options::make(array('new' => true));
		$categories = $validator->options->categories;

		$messages = Session::messages();

		return $this->tpl->display(__CLASS__.'::post_form', get_defined_vars());
	}

	// Same ass Add post, but now load a different Validator
	// We can use the same template though. Only minor checks in the template.
	public function edit_postAction( $post ) {
		$post = $this->getPost($post);
		if ( !$post->canEdit() ) {
			throw new NotFoundException('Editable post # '.$post->post_id);
		}

		$validator = models\Post::validator('edit');
		if ( !empty($_POST) ) {
			if ( $validator->validate($_POST) ) {
				if ( $post->update($validator->output) ) {
					Session::success('Post updated =) woohooo');
					$this->redirect($post->url());
				}
				Session::error('Couldn\'t save... =( Try again!?');
			}
		}

		$categories = models\Category::all();

		$messages = Session::messages();

		return $this->tpl->display(__CLASS__.'::post_form', get_defined_vars());
	}

	// If not logged in, the SessionUser->logout function will just ignore the call.
	public function logoutAction() {
		$this->user->logout();
		$this->redirect('/blog');
	}

	// I'm allowing double logins (or "login layers"):
	// If you're logged in, you can't reach the form, but if you pass a
	// UID, it'll just create another layer. Our SessionUser allows that (by default).
	// Validation (e.g. a password check) could come from a Validator but might
	// be overkill in this case. Our blog doesn't need a password though =)
	// Note how $this->post (typeof Options) can be used to fetch _POST data.
	public function loginAction( $uid = null ) {
		if ( null !== $uid ) {
			$this->user->login(models\User::get($uid));
		}
		if ( $this->user->isLoggedIn() ) {
			$this->redirect('/blog');
		}
		if ( !$this->post->isEmpty() ) {
			try {
				$user = models\User::one(array( 'username' => (string)$this->post->username ));
				$this->user->login($user);
				Session::success('Alright, alright, alright, you\'re logged in...');
				$this->redirect($this->post->get('goto', '/blog'));
			}
			catch ( \Exception $ex ) {}
			Session::error('Sorry, buddy, that\'s not your username!');
		}
		$messages = Session::messages();
		return $this->tpl->display(__METHOD__, get_defined_vars());
	}

	// See edit_post Action
	public function edit_commentAction( $comment ) {
		$comment = models\Comment::get($comment);
		if ( !$comment->canEdit() ) {
			throw new NotFoundException('Editable comment # '.$comment->comment_id);
		}

		$validator = models\Comment::validator('edit');
		if ( !empty($_POST) ) {
			if ( $validator->validate($_POST) ) {
				$update = $validator->output;
				if ( $comment->update($update) ) {
					Session::success('Comment changed');
					$this->redirect($comment->url());
				}
				Session::error('Didn\'t save... Try again!?');
			}
		}

		$messages = Session::messages();

		return $this->tpl->display(__CLASS__.'::comment_form', get_defined_vars());
	}

	// See add_post Action
	public function add_commentAction( $post ) {
		$post = $this->getPost($post);

		$anonymous = $this->user->isLoggedIn() ? '' : '_anonymous';
		$validator = models\Comment::validator('add'.$anonymous);
//echo '<pre>';
//print_r($validator); exit;
		if ( !empty($_POST) ) {
			if ( $validator->validate($_POST, $context) ) {
				$insert = $validator->output;
				if ( !$this->user->isLoggedIn() && isset($context['user']) ) {
					$this->user->login($context['user']);
				}
//print_r($insert); print_r($context); exit;
				$insert['post_id'] = $post->post_id;
				$insert['created_on'] = time();
				$insert['created_by_ip'] = $_SERVER['REMOTE_ADDR'];
//print_r($insert); exit;
				try {
					$cid = models\Comment::insert($insert);
//var_dump($cid); exit;
					$comment = models\Comment::get($cid);
//print_r($comment); exit;
					Session::success('Comment created');
					$this->redirect($comment->url());
				}
				catch ( \Exception $ex ) {
					Session::error('Didn\'t save... Try again!?');
				}
			}
			else {
				Session::error('See input errors below:');
			}
		}

		$comment = Options::make(array('new' => true));

		$messages = Session::messages();

		return $this->tpl->display(__CLASS__.'::comment_form', get_defined_vars());
	}

	// Test Action for a Route
	public function bestAction( $num = 900 ) {
		exit('Showing the '.$num.' best posts...');
	}

	// Most 'logic' and information comes from the Post Model
	public function viewAction( $post ) {

		$post = $this->getPost($post); // might throw a NotFound, which is caught outside the application

		$messages = Session::messages();

		return $this->tpl->display(__METHOD__, get_defined_vars());
	}

	// Two ways to get the right posts. Access is called within the Controller, not
	// the Model, because the Model doesn't have (as direct) access to the SessionUser.
	public function indexAction( $page = 1 ) {

		// Way 1
		// Define which get method to use to fetch Posts by checking ACL
		// Use that function and the Model's logic to get those posts.
		$unpub = $this->user->hasAccess('blog read unpublished');
		$method = $unpub ? 'newest' : 'newestPublished';
		$posts = models\Post::$method(self::config('posts_on_index'));

		// Way 2
		// Define the difference in conditions here (instead of in the Model)
		$conditions = $unpub ? '' : array('is_published' => true);
		$numAllPosts = models\Post::count($conditions);

		// Way 3
		// A third way would be a combination like this:
		 /*
		$access = $this->user->hasAccess('blog read unpublished');
		$posts = model\Post::postsByAccess($access, self::config('posts_on_index'));
		 */
		// That way you can check access in the Controller and have fetch logic in the Model

		$messages = Session::messages();

		return $this->tpl->display(__METHOD__, get_defined_vars());
	}

	/**
	 * Model update() test
	 * This method is actually never called from the blog... Just playing with the super-Models.
	 */
	public function commentAction( $id ) {
echo '<pre>time() = '.time()."\n";
		$comment = models\Comment::get($id);
		$update = $comment->update(array('created_on' => time())); // no placeholder stuff here!
		echo "Affected: ";
		var_dump($update);
		print_r($comment);
	}

	// A helper that checks user ACL and might throw a NotFoundException.
	// I want functionality like this in the Controller, not in a Model.
	protected function getPost( $post ) {
		try {
			$method = $this->user->hasAccess('blog read unpublished') ? 'get' : 'getPublishedPost';
			$post = models\Post::$method($post);
//			$post = models\Post::get($post);
			return $post;
		}
		catch ( \Exception $ex ) {
			throw new NotFoundException('Blog post # '.$post);
		}
	}

	// Testing Inflector methods
	public function inflectorAction() {
		echo "<pre><u>  camelcase:</u>\n\n";
		echo $txt = 'Oele boele la la';
		echo "\n";
		var_dump(Inflector::camelcase($txt));
		echo "\n";
		echo $txt = 'verified_user_address';
		echo "\n";
		var_dump(Inflector::camelcase($txt));
		echo "\n";
		echo "<u>  slugify:</u>\n\n";
		echo $txt = 'The (new) future of the old/young/restless AND... pretty!';
		echo "\n";
		var_dump(Inflector::slugify($txt));
		echo "\n";
		echo $txt = 'verified_user_address';
		echo "\n";
		var_dump(Inflector::slugify($txt));
		echo "\n";
		echo "<u>  spacify:</u>\n\n";
		echo $txt = 'the-new-future-of-the-old-young-restless-and-pretty';
		echo "\n";
		var_dump(Inflector::spacify($txt));
		echo "\n";
		echo $txt = 'verified_user_address';
		echo "\n";
		var_dump(Inflector::spacify($txt));
		echo "\n";
		echo "<u>  uncamelcase:</u>\n\n";
		echo $txt = 'verifiedUserAddress';
		echo "\n";
		var_dump(Inflector::uncamelcase($txt));
		echo '</pre>';
	}

}


