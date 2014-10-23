
Might be useful to provide a simplified "cheat sheet" of the flow of
control.

* public/index.php => Bootstrap.php => IndexController::indexAction =>
      redirects to appropriate controller/action
* redirection to controller/action actually breaks down to
     * preDispatch (which invokes Plugins in library/Ramp/Controller/Plugin/*,
	one of which causes menu to get loaded in and one performs
	authorization check of user role and resource in request
	against ACL rules)
     * controller/action -- code written in action function + automatic
        rendering, where data is passed to view by adding it as member
        data to $this->view in action function (e.g.,
        $this->view->message), which then becomes member of "this" in
        associated views/scripts/controller/action.phtml script (e.g.,
        $this->message).  Overall Ramp page layout is defined in
        layouts/scripts/layout.phtml.
     * postDispatch (Ramp not using this)

    Note: in Controllers, init method is extension of constructor (last
    step executed by constructor, which it is better not to redefine).
    So that flow is constructor (calls init at end), preDispatch,
    chosen action, postDispatch.  Rendering is either at end of
    chosen action or maybe even after postDispatch.

    Controllers get data from files or the database using classes
    in library/Ramp/Activity (activity-related classes) or in
    application/models (table setting-related classes are here, although
    they will probably move to library/Ramp/Settings sometime).
    Controllers also frequently build forms; those templates can be
    found in the application/forms directory.

Need instructions for how to run automated tests:
    cd to ramp/tests, then just type phpunit

Might want to say something about bootstrap process or the role of the
TestSettings file?


------------

<a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img
alt="Creative Commons License" style="border-width:0"
src="https://i.creativecommons.org/l/by/4.0/88x31.png" /></a><br /><span
xmlns:dct="http://purl.org/dc/terms/"
href="http://purl.org/dc/dcmitype/Text" property="dct:title"
rel="dct:type">RAMP Documentation</span> is licensed under a <a
rel="license"
href="http://creativecommons.org/licenses/by/4.0/">Creative Commons
Attribution 4.0 International License</a>.

