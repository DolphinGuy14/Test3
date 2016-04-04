<?php
/*  Copyright 2014-2016 GitBoss SRL <ping@GitBoss.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Gitium_Help {

	public function __construct( $hook, $help = 'gitboss' ) {
		add_action( "load-{$hook}", array( $this, $help ), 20 );
	}

	private function general() {
		$screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'gitboss', 'title' => __( 'gitboss', 'gitboss' ), 'callback' => array( $this, 'gitboss' ) ) );
		$screen->add_help_tab( array( 'id' => 'faq', 'title' => __( 'F.A.Q.', 'gitboss' ), 'callback' => array( $this, 'faq' ) ) );
		$screen->add_help_tab( array( 'id' => 'requirements', 'title' => __( 'Requirements', 'gitboss' ), 'callback' => array( $this, 'requirements_callback' ) ) );
		$screen->set_help_sidebar( '<div style="width:auto; height:auto; float:right; padding-right:28px; padding-top:15px"><img src="' . plugins_url( 'img/gitboss.svg', dirname( __FILE__ ) ) . '" width="96"></div>' );
	}

	public function gitboss() {
		echo '<p>' . __( 'gitboss enables continuous deployment for WordPress integrating with tools such as Github, Bitbucket or Travis-CI. Plugin and theme updates, installs and removals are automatically versioned.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'Ninja code edits from the WordPress editor are also tracked into version control. gitboss is designed for sane development environments.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'Staging and production can follow different branches of the same repository. You can deploy code simply trough git push.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'gitboss requires <code>git</code> command line tool minimum version 1.7 installed on the server and <code>proc_open</code> PHP function enabled.', 'gitboss' ) . '</p>';
	}

	public function faq() {
		echo '<p><strong>' . __( 'Is this plugin considered stable?', 'gitboss' ) . '</strong><br />'. __( 'Right now this plugin is considered alpha quality and should be used in production environments only by adventurous kinds.', 'gitboss' ) . '</p>';
		echo '<p><strong>' . __( 'What happens in case of conflicts?', 'gitboss' ) . '</strong><br />'. __( 'The behavior in case of conflicts is to overwrite the changes on the origin repository with the local changes (ie. local modifications take precedence over remote ones).', 'gitboss' ) . '</p>';
		echo '<p><strong>' . __( 'How to deploy automatically after a push?', 'gitboss' ) . '</strong><br />'. __( 'You can ping the webhook url after a push to automatically deploy the new code. The webhook url can be found under Code menu. This url plays well with Github or Bitbucket webhooks.', 'gitboss' ) . '</p>';
		echo '<p><strong>' . __( 'Does it works on multi site setups?', 'gitboss' ) . '</strong><br />'. __( 'gitboss is not supporting multisite setups at the moment.', 'gitboss' ) . '</p>';
		echo '<p><strong>' . __( 'How does gitboss handle submodules?', 'gitboss' ) . '</strong><br />'. __( 'Currently submodules are not supported.', 'gitboss' ) . '</p>';
	}

	public function requirements_callback() {
		echo '<p>' . __( 'gitboss requires:', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'the function proc_open available', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'can exec the file inc/ssh-git', 'gitboss' ) . '</p>';

		printf( '<p>' . __( 'git version >= %s', 'gitboss' ) . '</p>', GITIUM_MIN_GIT_VER );
		printf( '<p>' . __( 'PHP version >= %s', 'gitboss' ) . '</p>', GITIUM_MIN_PHP_VER );
	}

	public function configuration() {
		$screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'configuration', 'title' => __( 'Configuration', 'gitboss' ), 'callback' => array( $this, 'configuration_callback' ) ) );
		$this->general();
	}

	public function configuration_callback() {
		echo '<p><strong>' . __( 'Configuration step 1', 'gitboss' ) . '</strong><br />' . __( 'In this step you must specify the <code>Remote URL</code>. This URL represents the link between the git sistem and your site.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'You can get this URL from your Git repository and it looks like this:', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'github.com -> git@github.com:user/example.git', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'bitbucket.org -> git@bitbucket.org:user/glowing-happiness.git', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'To go to the next step, fill the <code>Remote URL</code> and then press the <code>Fetch</code> button.', 'gitboss' ) . '</p>';
		echo '<p><strong>' . __( 'Configuration step 2', 'gitboss' ) . '</strong><br />' . __( 'In this step you must select the <code>branch</code> you want to follow.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'Only this branch will have all of your code modifications.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'When you push the button <code>Merge & Push</code>, all code(plugins & themes) will be pushed on the git repository.', 'gitboss' ) . '</p>';
	}

	public function status() {
		$screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'status', 'title' => __( 'Status', 'gitboss' ), 'callback' => array( $this, 'status_callback' ) ) );
		$this->general();
	}

	public function status_callback() {
		echo '<p>' . __( 'On status page you can see what files are modified, and you can commit the changes to git.', 'gitboss' ) . '</p>';
	}

	public function commits() {
		$screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'commits', 'title' => __( 'Commits', 'gitboss' ), 'callback' => array( $this, 'commits_callback' ) ) );
		$this->general();
	}

	public function commits_callback() {
		echo '<p>' . __( 'You may be wondering what is the difference between author and committer.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'The <code>author</code> is the person who originally wrote the patch, whereas the <code>committer</code> is the person who last applied the patch.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'So, if you send in a patch to a project and one of the core members applies the patch, both of you get credit — you as the author and the core member as the committer.', 'gitboss' ) . '</p>';
	}

	public function settings() {
		$screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'settings', 'title' => __( 'Settings', 'gitboss' ), 'callback' => array( $this, 'settings_callback' ) ) );
		$this->general();
	}

	public function settings_callback() {
		echo '<p>' . __( 'Each line from the gitignore file specifies a pattern.', 'gitboss' ) . '</p>';
		echo '<p>' . __( 'When deciding whether to ignore a path, Git normally checks gitignore patterns from multiple sources, with the following order of precedence, from highest to lowest (within one level of precedence, the last matching pattern decides the outcome)', 'gitboss' ) . '</p>';
		echo '<p>' . sprintf( __( 'Read more on %s', 'gitboss' ), '<a href="http://git-scm.com/docs/gitignore" target="_blank">git documentation</a>' ) . '</p>';
	}
}
