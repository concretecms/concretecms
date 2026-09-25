## Installation Instructions for Concrete CMS

	1.	Make sure your application/config/ directory is writable by a web server. (Note, this is the application/config/ directory in the root of the archive).
	2.	Make sure application/files/ and its subdirectories are writable by the web server (or the world.)
	3.	Create a new MySQL database and a MySQL user account with the following privileges on that database: INSERT, SELECT, UPDATE, DELETE, CREATE, DROP, ALTER
	4.	Visit your Concrete website in your web browser. You should see an installation screen where you can step through the process of entering your database details, your administrative username details and more. 
	5.	Upon completing this process, Concrete should be installed.
	
## Primary/Replica Database Configuration

The database connection in `application/config/database.php` can use one or more
read replicas. Existing top-level settings remain the primary connection and the
defaults inherited by each replica:

```php
'connections' => [
    'concrete' => [
        'driver' => 'concrete_pdo_mysql',
        'server' => 'writer.example',
        'database' => 'app',
        'username' => 'app',
        'password' => 'secret',
        'character_set' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',

        // Optional overrides for the primary connection.
        'primary' => [
            'server' => 'writer.example',
        ],

        // Replica settings inherit the finalized primary settings.
        'replica' => [
            ['server' => 'reader-a.example'],
            ['server' => 'reader-b.example'],
        ],

        // Preserve the replica connection for an explicit switch back.
        'keepReplica' => true,
    ],
],
```

DBAL read methods such as `executeQuery()` and the fetch helpers use a replica
until the primary is selected. Write methods such as `executeStatement()`,
transactions, prepared statements, and the deprecated `query()` and
`Execute()` APIs always select the primary. Routing is based on the DBAL method,
not SQL inspection: write SQL passed to `executeQuery()` will be sent to a
replica.

After the primary is selected, reads stay on it to provide read-your-writes
consistency. With `keepReplica` enabled, application code may explicitly call
`ensureConnectedToReplica()`; doing so can return stale data when replication
is delayed.


## Simpler Installation

concretecms.com offers hosting and will pre-install Concrete for you:

	Concrete CMS Hosting
	http://www.concretecms.com/hosting/
	
Concrete CMS can also be installed with one click on other web hosts by using Softaculous or SimpleScripts. Check with your web host to see if you have these services enabled.
