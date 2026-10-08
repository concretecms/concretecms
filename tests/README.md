## Step 1: Clone this repository.

Or download it.


## Step 2: Setup concrete5

As described [here](../README.md#installation)


## Step 3: Setup the database

The tests expect a MySQL server on the same computer, reachable with the credentials set in `tests/assets/config/database.php` (by default `root` with password `root` on `127.0.0.1`).
Every run starts by dropping and creating the test database, so that account needs full privileges on it.
If you prefer a dedicated account, create it like this and set it in `database.php`:

```sql
CREATE USER 'ccm_test'@'localhost' IDENTIFIED BY '';
GRANT ALL ON `ccm\_tests%`.* TO 'ccm_test'@'localhost';
FLUSH PRIVILEGES;
```

The `ccm_tests%` pattern covers the databases of the parallel runs described below.


## Step 4: Run the tests!

Run

```sh
composer test
```

from within the root directory (not the tests folder).

To run a single tests, you can run for example

```sh
composer test -- --filter testCoreBlockView
```

Every run recreates the test database and empties a temporary directory, which receives the log (`logs/tests.log`), a copy of the `tests/assets/config` configuration and the files written by the tests.


## Running more test processes at the same time

Two test runs must not share the database nor the temporary directory: give each process its own run ID with the `CCM_TESTS_RUNID` environment variable (a positive integer, default: `1`).
A run uses the `tests/tmp/run<runID>` temporary directory and the `ccm_tests<runID>` database (just `ccm_tests` for the default run ID).

For example:

```sh
CCM_TESTS_RUNID=2 composer test -- tests/tests/Page
```

Some tests write to fixed paths of the repository (for example `application`, `packages` and `tests/helpers/File/files`), so the processes must also run in different checkouts of the repository (for example git worktrees).


# Write Tests!

Send us tests via pull request:
- actual test classes must go to the /tests/tests folder (classes must be defined in a namespace starting with Concrete\Tests\...)
- helper classes must go to the /tests/helpers folder (classes must be defined in a namespace starting with Concrete\TestHelpers\...)
- other files must go to the /tests/assets folder (images, fake classes, ...)
