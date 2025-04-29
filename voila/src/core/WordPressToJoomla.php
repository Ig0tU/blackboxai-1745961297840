<?php
namespace Voila\Core;

use Voila\Mappers\Dictionary;
use Voila\Utils\DatabaseHelper;

class WordPressToJoomla extends Converter
{
    private $dictionary;
    private $wpDb;
    private $joomlaDb;

    public function __construct($logger, $wpDbConfig, $joomlaDbConfig)
    {
        parent::__construct($logger);
        $this->dictionary = new \Voila\Mappers\Dictionary();
        $this->wpDb = new \Voila\Utils\DatabaseHelper(
            $wpDbConfig['host'],
            $wpDbConfig['dbname'],
            $wpDbConfig['username'],
            $wpDbConfig['password']
        );
        $this->joomlaDb = new \Voila\Utils\DatabaseHelper(
            $joomlaDbConfig['host'],
            $joomlaDbConfig['dbname'],
            $joomlaDbConfig['username'],
            $joomlaDbConfig['password']
        );
    }

    public function migrate()
    {
        $this->logger->info("Starting WordPress to Joomla migration...");

        // Example usage of dictionary mapping
        $wpFunc = "wp_get_posts";
        $commonTerm = $this->dictionary->getCommonTermFromWordPress($wpFunc);
        $joomlaFunc = $this->dictionary->getJoomlaFunction($commonTerm);

        $this->logger->info("Mapping WordPress function '{$wpFunc}' to Joomla function '{$joomlaFunc}'");

        // Content migration example
        $this->migratePosts();
    }

    private function migratePosts()
    {
        $this->logger->info("Migrating posts from WordPress to Joomla...");

        try {
            // Fetch posts from WordPress DB
            $wpPosts = $this->fetchWordPressPosts();

            foreach ($wpPosts as $post) {
                // Fetch post metadata
                $postMeta = $this->fetchWordPressPostMeta($post['ID']);

                // Map WordPress post fields and metadata to Joomla article fields
                $joomlaArticle = $this->mapPostToArticle($post, $postMeta);

                // Save Joomla article (placeholder)
                $this->saveJoomlaArticle($joomlaArticle);

                $this->logger->info("Migrated post ID {$post['ID']} to Joomla article.");
            }
        } catch (\Exception $e) {
            $this->logger->error("Error migrating posts: " . $e->getMessage());
        }

        // Migrate categories
        $this->migrateCategories();

        // Migrate users
        $this->migrateUsers();
    }

    private function fetchWordPressPostMeta($postId)
    {
        $sql = "SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = :post_id";
        $stmt = $this->wpDb->query($sql, [':post_id' => $postId]);
        $meta = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $meta[$row['meta_key']] = $row['meta_value'];
        }
        return $meta;
    }

    private function mapPostToArticle($post, $postMeta = [])
    {
        // Use common dictionary to map elements
        $element = $this->dictionary->getElementByName('accordion'); // Example: using 'accordion' as an example element

        $article = [
            'title' => $post['post_title'],
            'content' => $post['post_content'],
            'created' => $post['post_date']
        ];

        // Map custom fields from postMeta if needed
        if (!empty($postMeta)) {
            // Example: map a custom field 'subtitle' to Joomla article's 'alias'
            if (isset($postMeta['subtitle'])) {
                $article['alias'] = $postMeta['subtitle'];
            }
        }

        // Additional mapping logic can be added here using $element properties

        return $article;
    }

    private function migrateCategories()
    {
        $this->logger->info("Migrating categories from WordPress to Joomla...");

        try {
            $wpCategories = $this->fetchWordPressCategories();

            foreach ($wpCategories as $category) {
                $joomlaCategory = $this->mapCategoryToJoomla($category);
                $this->saveJoomlaCategory($joomlaCategory);
                $this->logger->info("Migrated category ID {$category['term_id']} to Joomla category.");
            }
        } catch (\Exception $e) {
            $this->logger->error("Error migrating categories: " . $e->getMessage());
        }
    }

    private function migrateUsers()
    {
        $this->logger->info("Migrating users from WordPress to Joomla...");

        try {
            $wpUsers = $this->fetchWordPressUsers();

            foreach ($wpUsers as $user) {
                $joomlaUser = $this->mapUserToJoomla($user);
                $this->saveJoomlaUser($joomlaUser);
                $this->logger->info("Migrated user ID {$user['ID']} to Joomla user.");
            }
        } catch (\Exception $e) {
            $this->logger->error("Error migrating users: " . $e->getMessage());
        }
    }

    private function fetchWordPressCategories()
    {
        $sql = "SELECT term_id, name, slug FROM wp_terms";
        return $this->wpDb->fetchAll($sql);
    }

    private function fetchWordPressUsers()
    {
        $sql = "SELECT ID, user_login, user_email, user_registered FROM wp_users";
        return $this->wpDb->fetchAll($sql);
    }

    private function mapCategoryToJoomla($category)
    {
        return [
            'title' => $category['name'],
            'alias' => $category['slug']
        ];
    }

    private function mapUserToJoomla($user)
    {
        return [
            'username' => $user['user_login'],
            'email' => $user['user_email'],
            'registerDate' => $user['user_registered']
        ];
    }

    private function saveJoomlaCategory($category)
    {
        $sql = "INSERT INTO #__categories (title, alias) VALUES (:title, :alias)";
        $params = [
            ':title' => $category['title'],
            ':alias' => $category['alias']
        ];

        try {
            $this->joomlaDb->query($sql, $params);
            $this->logger->info("Joomla category '{$category['title']}' saved successfully.");
        } catch (\Exception $e) {
            $this->logger->error("Failed to save Joomla category '{$category['title']}': " . $e->getMessage());
        }
    }

    private function saveJoomlaUser($user)
    {
        $sql = "INSERT INTO #__users (username, email, registerDate) VALUES (:username, :email, :registerDate)";
        $params = [
            ':username' => $user['username'],
            ':email' => $user['email'],
            ':registerDate' => $user['registerDate']
        ];

        try {
            $this->joomlaDb->query($sql, $params);
            $this->logger->info("Joomla user '{$user['username']}' saved successfully.");
        } catch (\Exception $e) {
            $this->logger->error("Failed to save Joomla user '{$user['username']}': " . $e->getMessage());
        }
    }

    private function fetchWordPressPosts()
    {
        $sql = "SELECT ID, post_title, post_content, post_date FROM wp_posts WHERE post_status = 'publish' AND post_type = 'post'";
        return $this->wpDb->fetchAll($sql);
    }

    // Removed duplicate mapPostToArticle method to fix redeclaration error

    private function saveJoomlaArticle($article)
    {
        $this->logger->info("Saving Joomla article titled '{$article['title']}'");

        $sql = "INSERT INTO #__content (title, introtext, created) VALUES (:title, :introtext, :created)";
        $params = [
            ':title' => $article['title'],
            ':introtext' => $article['content'],
            ':created' => $article['created']
        ];

        try {
            $this->joomlaDb->query($sql, $params);
            $this->logger->info("Joomla article '{$article['title']}' saved successfully.");
        } catch (\Exception $e) {
            $this->logger->error("Failed to save Joomla article '{$article['title']}': " . $e->getMessage());
        }
    }
}
