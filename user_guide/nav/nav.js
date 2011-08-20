function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<h3>General</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/config.html">Configuration</a></li>' +
			'<li><a href="'+base+'general/validation.html">Form Validation</a></li>' +
			'<li><a href="'+base+'general/utility.html">Utility Functions</a></li>' +
			'<li><a href="'+base+'general/notes.html">Notes</a></li>' +
		'</ul>' +

		'</td><td class="td_sep" valign="top">' +
		'<h3>Users</h3>' +
		'<ul>' +
			'<li><a href="'+base+'users/logging_in.html">Logging In/Logging Out</a></li>' +
			'<li><a href="'+base+'users/customizing.html">Customizing User Data</a></li>' +
			'<li><a href="'+base+'users/fetching.html">Fetching Users</a></li>' +
			'<li><a href="'+base+'users/adding.html">Adding Users</a></li>' +
			'<li><a href="'+base+'users/editing.html">Editing Users</a></li>' +
			'<li><a href="'+base+'users/assigning.html">Assigning Groups</a></li>' +
		'</ul>' +
		'</td><td class="td_sep" valign="top">' +
		'<h3>Groups</h3>' +
		'<ul>' +
			'<li><a href="'+base+'groups/fetching.html">Fetching Groups</a></li>' +
			'<li><a href="'+base+'groups/adding.html">Adding Groups</a></li>' +
			'<li><a href="'+base+'groups/editing.html">Editing Groups</a></li>' +
			'<li><a href="'+base+'groups/assigning.html">Adding Members</a></li>' +
		'</ul>' +
		'</td><td class="td_sep" valign="top">' +
		'<h3>Roles</h3>' +
		'<ul>' +
			'<li><a href="'+base+'roles/creating.html">Creating Roles</a></li>' +
			'<li><a href="'+base+'roles/setting.html">Setting Roles</a></li>' +
			'<li><a href="'+base+'roles/checking.html">Checking Roles</a></li>' +
		'</ul>' +

		'</td></tr></table>');
}