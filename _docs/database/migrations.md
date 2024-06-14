# Migrations

Migrations allow you to easily update your database schema and keep it in sync with your application. They can be committed to your version control system, and easily shared with other developers. Monarch provides a simple and lightweight migration system.

## Creating Migrations

Migrations are simple SQL files. They are stored in the `database/migrations` directory. Each migration file should have a unique name, and should contain the SQL queries to update the database schema. The migration files are executed in the order the operating system finds them. It's up to you to ensure that the migrations are executed in the correct order. One way to do this is to prefix the migration files with a timestamp or a version number. For example:

```
database/migrations/20220101120000_create_users_table.sql
database/migrations/20220101120100_create_posts_table.sql
```

or

```
database/migrations/v1_create_users_table.sql
database/migrations/v2_create_posts_table.sql
```

Migrations are discovered within sub-directories of the `database/migrations` directory. This allows you to group your migrations by app release version, or by feature, for example. The sub-directories are scanned recursively, so you can nest them as deep as you like.

```
database/
    migrations/
        v1.0.0/
            create_users_table.sql
            create_posts_table.sql
        v1.1.0/
            add_email_to_users_table.sql
```

## Running Migrations

To run the migrations, you can use the `migrate` command. This command will execute all the migrations that have not yet been executed. It will also keep track of which migrations have been executed, so that they are not executed again. You can run the command like this:

```
php console migrate
```

This will execute all the migrations in the `database/migrations` directory, in the order the operating system finds them. If you want to execute only the migrations in a specific sub-directory, you can pass the sub-directory name as an argument:

```
php console migrate v1.0.0
```

This will execute only the migrations in the `database/migrations/v1.0.0` directory.

If you want to execute only a specific migration file, you can pass the file name as an argument. The file name must be relative to the `database/migrations` directory:

```
php console migrate v1.0.0/create_users_table.sql
```

### Refreshing Migrations

If you want to re-run all the migrations, you can use the `migrate --fresh` command. This command will drop all existing tables, and then re-run all migrations. You can run the command like this:

```
php console migrate --fresh

// or

php console migrate -F
```

### Writing Migrations

Migrations are simple SQL files. They should contain the SQL queries to update the database schema. For example:

```sql
-- database/migrations/create_users_table.sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);
```
