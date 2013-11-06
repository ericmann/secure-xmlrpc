<?php
include_once(ABSPATH . 'wp-admin/includes/admin.php');
include_once(ABSPATH . WPINC . '/class-IXR.php');
include_once(ABSPATH . WPINC . '/class-wp-xmlrpc-server.php');

/**
 * Secure XML-RPC Server Implementation
 *
 * Extends OAuth-style security for remote procedure calls so they don't need to pass username/password credentials
 * in plaintext with the request.
 *
 * @subpackage Publishing
 */
class secure_xmlrpc_server extends wp_xmlrpc_server {

	/**
	 * Register all of the XML-RPC overrides we need to properly secure WordPress
	 *
	 * @return secure_xmlrpc_server
	 */
	public function __construct() {
		add_filter( 'xmlrpc_methods', array( $this, 'add_methods' ), 10, 1 );

		parent::__construct();
	}

	/**
	 * Filter default methods and overload with our secure implementation.
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	public function add_methods( $methods ) {
		// WordPress API
		$methods['wp.getUsersBlogs']        = array ( $this, 'wp_getUsersBlogs' );
		$methods['wp.newPost']              = array ( $this, 'wp_newPost' );
		$methods['wp.editPost']             = array ( $this, 'wp_editPost' );
		$methods['wp.deletePost']           = array ( $this, 'wp_deletePost' );
		$methods['wp.getPost']              = array ( $this, 'wp_getPost' );
		$methods['wp.getPosts']             = array ( $this, 'wp_getPosts' );
		$methods['wp.newTerm']              = array ( $this, 'wp_newTerm' );
		$methods['wp.editTerm']             = array ( $this, 'wp_editTerm' );
		$methods['wp.deleteTerm']           = array ( $this, 'wp_deleteTerm' );
		$methods['wp.getTerm']              = array ( $this, 'wp_getTerm' );
		$methods['wp.getTerms']             = array ( $this, 'wp_getTerms' );
		$methods['wp.getTaxonomy']          = array ( $this, 'wp_getTaxonomy' );
		$methods['wp.getTaxonomies']        = array ( $this, 'wp_getTaxonomies' );
		$methods['wp.getUser']              = array ( $this, 'wp_getUser' );
		$methods['wp.getUsers']             = array ( $this, 'wp_getUsers' );
		$methods['wp.getProfile']           = array ( $this, 'wp_getProfile' );
		$methods['wp.editProfile']          = array ( $this, 'wp_editProfile' );
		$methods['wp.getPage']              = array ( $this, 'wp_getPage' );
		$methods['wp.getPages']             = array ( $this, 'wp_getPages' );
		$methods['wp.newPage']              = array ( $this, 'wp_newPage' );
		$methods['wp.deletePage']           = array ( $this, 'wp_deletePage' );
		$methods['wp.editPage']             = array ( $this, 'wp_editPage' );
		$methods['wp.getPageList']          = array ( $this, 'wp_getPageList' );
		$methods['wp.getAuthors']           = array ( $this, 'wp_getAuthors' );
		$methods['wp.getCategories']        = array ( $this, 'mw_getCategories' );       // Alias
		$methods['wp.getTags']              = array ( $this, 'wp_getTags' );
		$methods['wp.newCategory']          = array ( $this, 'wp_newCategory' );
		$methods['wp.deleteCategory']       = array ( $this, 'wp_deleteCategory' );
		$methods['wp.suggestCategories']    = array ( $this, 'wp_suggestCategories' );
		$methods['wp.uploadFile']           = array ( $this, 'mw_newMediaObject' ); // Alias
		$methods['wp.getCommentCount']      = array ( $this, 'wp_getCommentCount' );
		$methods['wp.getPostStatusList']    = array ( $this, 'wp_getPostStatusList' );
		$methods['wp.getPageStatusList']    = array ( $this, 'wp_getPageStatusList' );
		$methods['wp.getPageTemplates']     = array ( $this, 'wp_getPageTemplates' );
		$methods['wp.getOptions']           = array ( $this, 'wp_getOptions' );
		$methods['wp.setOptions']           = array ( $this, 'wp_setOptions' );
		$methods['wp.getComment']           = array ( $this, 'wp_getComment' );
		$methods['wp.getComments']          = array ( $this, 'wp_getComments' );
		$methods['wp.deleteComment']        = array ( $this, 'wp_deleteComment' );
		$methods['wp.editComment']          = array ( $this, 'wp_editComment' );
		$methods['wp.newComment']           = array ( $this, 'wp_newComment' );
		$methods['wp.getCommentStatusList'] = array ( $this, 'wp_getCommentStatusList' );
		$methods['wp.getMediaItem']         = array ( $this, 'wp_getMediaItem' );
		$methods['wp.getMediaLibrary']      = array ( $this, 'wp_getMediaLibrary' );
		$methods['wp.getPostFormats']       = array ( $this, 'wp_getPostFormats' );
		$methods['wp.getPostType']          = array ( $this, 'wp_getPostType' );
		$methods['wp.getPostTypes']         = array ( $this, 'wp_getPostTypes' );
		$methods['wp.getRevisions']         = array ( $this, 'wp_getRevisions' );
		$methods['wp.restoreRevision']      = array ( $this, 'wp_restoreRevision' );

		// Blogger API
		$methods['blogger.getUsersBlogs']  = array ( $this, 'blogger_getUsersBlogs' );
		$methods['blogger.getUserInfo']    = array ( $this, 'blogger_getUserInfo' );
		$methods['blogger.getPost']        = array ( $this, 'blogger_getPost' );
		$methods['blogger.getRecentPosts'] = array ( $this, 'blogger_getRecentPosts' );
		$methods['blogger.newPost']        = array ( $this, 'blogger_newPost' );
		$methods['blogger.editPost']       = array ( $this, 'blogger_editPost' );
		$methods['blogger.deletePost']     = array ( $this, 'blogger_deletePost' );

		// MetaWeblog API
		$methods['metaWeblog.newPost']        = array ( $this, 'mw_newPost' );
		$methods['metaWeblog.editPost']       = array ( $this, 'mw_editPost' );
		$methods['metaWeblog.getPost']        = array ( $this, 'mw_getPost' );
		$methods['metaWeblog.getRecentPosts'] = array ( $this, 'mw_getRecentPosts' );
		$methods['metaWeblog.getCategories']  = array ( $this, 'mw_getCategories' );
		$methods['metaWeblog.newMediaObject'] = array ( $this, 'mw_newMediaObject' );
		$methods['metaWeblog.deletePost']     = array ( $this, 'blogger_deletePost' );
		$methods['metaWeblog.getUsersBlogs']  = array ( $this, 'blogger_getUsersBlogs' );

		// MovableType API
		$methods['mt.getCategoryList']      = array ( $this, 'mt_getCategoryList' );
		$methods['mt.getRecentPostTitles']  = array ( $this, 'mt_getRecentPostTitles' );
		$methods['mt.getPostCategories']    = array ( $this, 'mt_getPostCategories' );
		$methods['mt.setPostCategories']    = array ( $this, 'mt_setPostCategories' );
		$methods['mt.publishPost']          = array ( $this, 'mt_publishPost' );

		return $methods;
	}

	/**
	 * Add an X-Deprecated header.
	 *
	 * @param string $message
	 */
	protected function deprecated( $message = '' ) {
		if ( empty( $message ) )
			$message = __( 'Username/Password authentication is deprecated for XML-RPC requests.', 'xmlrpcs' );

		header( 'X-Deprecated: ' . $message );
	}

	// WordPress API Overloads

	/**
	 * Overload the existing wp.getUsersBlogs method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getUsersBlogs( $args ) {
		if ( ! empty( $args[0] ) || ! empty( $args[1] ) )
			$this->deprecated();

		return parent::wp_getUsersBlogs( $args );
	}

	/**
	 * Overload the existing wp.newPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_newPost( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_newPost( $args );
	}

	/**
	 * Overload the existing wp.editPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_editPost( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_editPost( $args );
	}

	/**
	 * Overload the existing wp.deletePost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_deletePost( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_deletePost( $args );
	}

	/**
	 * Overload the existing wp.getPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPost( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPost( $args );
	}

	/**
	 * Overload the existing wp.getPosts method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPosts( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPosts( $args );
	}

	/**
	 * Overload the existing wp.newTerm method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_newTerm( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_newTerm( $args );
	}

	/**
	 * Overload the existing wp.editTerm method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_editTerm( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_editTerm( $args );
	}

	/**
	 * Overload the existing wp.deleteTerm method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_deleteTerm( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_deleteTerm( $args );
	}

	/**
	 * Overload the existing wp.getTerm method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getTerm( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getTerm( $args );
	}

	/**
	 * Overload the existing wp.getTerms method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getTerms( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getTerms( $args );
	}

	/**
	 * Overload the existing wp.getTaxonomy method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getTaxonomy( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getTaxonomy( $args );
	}

	/**
	 * Overload the existing wp.getTaxonomies method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getTaxonomies( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getTaxonomies( $args );
	}

	/**
	 * Overload the existing wp.getUser method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getUser( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getUser( $args );
	}

	/**
	 * Overload the existing wp.getUsers method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getUsers( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getUsers( $args );
	}

	/**
	 * Overload the existing wp.getProfile method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getProfile( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getProfile( $args );
	}

	/**
	 * Overload the existing wp.editProfile method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_editProfile( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_editProfile( $args );
	}

	/**
	 * Overload the existing wp.getPage method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPage($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPage( $args );
	}

	/**
	 * Overload the existing wp.getPages method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPages($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPages( $args );
	}

	/**
	 * Overload the existing wp.newPage method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_newPage($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_newPage( $args );
	}

	/**
	 * Overload the existing wp.deletePage method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_deletePage($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_deletePage( $args );
	}

	/**
	 * Overload the existing wp.editPage method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_editPage($args) {
		if ( ! empty( $args[2] ) || ! empty( $args[3] ) )
			$this->deprecated();

		return parent::wp_editPage( $args );
	}

	/**
	 * Overload the existing wp.getPageList method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPageList($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPageList( $args );
	}

	/**
	 * Overload the existing wp.getAuthors method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getAuthors($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getAuthors( $args );
	}

	/**
	 * Overload the existing wp.getTags method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getTags( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getTags( $args );
	}

	/**
	 * Overload the existing wp.newCategory method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_newCategory($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_newCategory( $args );
	}

	/**
	 * Overload the existing wp.deleteCategory method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_deleteCategory($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_deleteCategory( $args );
	}

	/**
	 * Overload the existing wp.suggestCategories method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_suggestCategories($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_suggestCategories( $args );
	}

	/**
	 * Overload the existing wp.getComment method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getComment($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getComment( $args );
	}

	/**
	 * Overload the existing wp.getComments method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getComments($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getComments( $args );
	}

	/**
	 * Overload the existing wp.deleteComment method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_deleteComment($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_deleteComment( $args );
	}

	/**
	 * Overload the existing wp.editComment method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_editComment($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_editComment( $args );
	}

	/**
	 * Overload the existing wp.newComment method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_newComment($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_newComment( $args );
	}

	/**
	 * Overload the existing wp.getCommentStatusList method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getCommentStatusList($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getCommentStatusList( $args );
	}

	/**
	 * Overload the existing wp.getCommentCount method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getCommentCount( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getCommentCount( $args );
	}

	/**
	 * Overload the existing wp.getPostStatusList method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPostStatusList( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPostStatusList( $args );
	}

	/**
	 * Overload the existing wp.getPageStatusList method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPageStatusList( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPageStatusList( $args );
	}

	/**
	 * Overload the existing wp.getPageTemplates method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPageTemplates( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPageTemplates( $args );
	}

	/**
	 * Overload the existing wp.getOptions method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getOptions( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getOptions( $args );
	}

	/**
	 * Overload the existing wp.setOptions method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_setOptions( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_setOptions( $args );
	}

	/**
	 * Overload the existing wp.getMediaItem method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getMediaItem($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getMediaItem( $args );
	}

	/**
	 * Overload the existing wp.getMediaLibrary method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getMediaLibrary($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getMediaLibrary( $args );
	}

	/**
	 * Overload the existing wp.getPostFormats method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPostFormats( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPostFormats( $args );
	}
	/**
	 * Overload the existing wp.getPostType method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */

	public function wp_getPostType( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPostType( $args );
	}

	/**
	 * Overload the existing wp.getPostTypes method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getPostTypes( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getPostTypes( $args );
	}

	/**
	 * Overload the existing wp.getRevisions method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_getRevisions( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_getRevisions( $args );
	}

	/**
	 * Overload the existing wp.restoreRevision method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function wp_restoreRevision( $args ) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::wp_restoreRevision( $args );
	}

	// Blogger API Overloads

	/**
	 * Overload the existing blogger.getUsersBlogs method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function blogger_getUsersBlogs($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::blogger_getUsersBlogs( $args );
	}

	/**
	 * Overload the existing blogger.getUserInfo method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function blogger_getUserInfo($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::blogger_getUserInfo( $args );
	}

	/**
	 * Overload the existing blogger.getPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function blogger_getPost($args) {
		if ( ! empty( $args[2] ) || ! empty( $args[3] ) )
			$this->deprecated();

		return parent::blogger_getPost( $args );
	}

	/**
	 * Overload the existing blogger.getRecentPosts method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function blogger_getRecentPosts($args) {
		if ( ! empty( $args[2] ) || ! empty( $args[3] ) )
			$this->deprecated();

		return parent::blogger_getRecentPosts( $args );
	}

	/**
	 * Overload the existing blogger.newPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function blogger_newPost($args) {
		if ( ! empty( $args[2] ) || ! empty( $args[3] ) )
			$this->deprecated();

		return parent::blogger_newPost( $args );
	}

	/**
	 * Overload the existing blogger.editPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function blogger_editPost($args) {
		if ( ! empty( $args[2] ) || ! empty( $args[3] ) )
			$this->deprecated();

		return parent::blogger_editPost( $args );
	}

	/**
	 * Overload the existing blogger.deletePost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function blogger_deletePost($args) {
		if ( ! empty( $args[2] ) || ! empty( $args[3] ) )
			$this->deprecated();

		return parent::blogger_deletePost( $args );
	}

	// MetaWeblog API Overloads

	/**
	 * Overload the existing metaWeblog.newPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mw_newPost($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mw_newPost( $args );
	}

	/**
	 * Overload the existing metaWeblog.editPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mw_editPost($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mw_editPost( $args );
	}

	/**
	 * Overload the existing metaWeblog.getPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mw_getPost($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mw_getPost( $args );
	}

	/**
	 * Overload the existing metaWeblog.getRecentPosts method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mw_getRecentPosts($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mw_getRecentPosts( $args );
	}

	/**
	 * Overload the existing metaWeblog.getCategories method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mw_getCategories($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mw_getCategories( $args );
	}

	/**
	 * Overload the existing metaWeblog.newMediaObject method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mw_newMediaObject($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mw_newMediaObject( $args );
	}

	// MovableType API Overloads

	/**
	 * Overload the existing mt.getRecentPostTitles method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mt_getRecentPostTitles($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mt_getRecentPostTitles( $args );
	}

	/**
	 * Overload the existing mt.getCategoryList method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mt_getCategoryList($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mt_getCategoryList( $args );
	}

	/**
	 * Overload the existing mt.getPostCategories method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mt_getPostCategories($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mt_getPostCategories( $args );
	}

	/**
	 * Overload the existing mt.setPostCategories method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mt_setPostCategories($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mt_setPostCategories( $args );
	}

	/**
	 * Overload the existing mt.publishPost method.
	 *
	 * @param array $args
	 *
	 * @return array|void
	 */
	public function mt_publishPost($args) {
		if ( ! empty( $args[1] ) || ! empty( $args[2] ) )
			$this->deprecated();

		return parent::mt_publishPost( $args );
	}
}