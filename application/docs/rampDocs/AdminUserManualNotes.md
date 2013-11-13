
  -- New users (including DBAs when accounts first created) are given a
  default password.  This causes RAMP to redirect the user to a Set
  Password screen the first time they try to log in.  There is now an
  Active? field in the Users table, so new users can be created as
  Inactive to reduce the chance that a malicious user will come in and
  set the password before the new user has a chance to do so.  The new
  user should be set to Active just before they are expected to use the
  system for the first time.


Need good instructions for doing backups, restoring from backups (???),
staging Ramp upgrades.  (New version should be put in a new directory
structure and should use a cloned version of the database.  Once a new
version has been tested and is ready
to go into production, Would be good to include script that tars up
the version being replaced -- code & settings -- and saves it along with
a snapshot of the database at that moment?)
