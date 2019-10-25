<?php

class Book
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getRandomBooks()
    {
        $this->db->query("SELECT * FROM items ORDER BY RAND() LIMIT 4");
        $results = $this->db->resultSet();
        return $results;
    }

    public function checkBookIfOrdered($bookId)
    {
        $this->db->query("SELECT * FROM order_details WHERE item_id = :item_id");
        $this->db->bind(":item_id", $bookId);
        $this->db->execute();
        if ($this->getRowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getBooksBySearch($name, $genre)
    {
        $search = "%$name%";
        $sql = "SELECT * FROM items i 
                    LEFT JOIN items_genres ig ON i.id = ig.item_id 
                    LEFT JOIN genres g ON ig.genre_id = g.id 
                    WHERE i.name LIKE :search ";
        $binds[":search"] = $search;
        if ($genre != "0") {
            $sql .= " AND genre = :genre ";
            $binds[":genre"] = $genre;
        }
        $sql .= " ORDER BY created_at DESC";

        $this->db->queryWithParams($sql, $binds);
        $results = $this->db->resultSet();
        return $results;
    }

    public function getBooksByPaginationSearch($name, $genre, $offset, $limiter)
    {
        $search = "%$name%";
        $sql = "SELECT items.id, name, description, price, image, created_at 
                    FROM items 
                    LEFT JOIN items_genres ON items.id = items_genres.item_id 
                    LEFT JOIN genres ON items_genres.genre_id = genres.id 
                    WHERE name LIKE :search ";
        $binds[":search"] = $search;
        if ($genre != "0") {
            $sql .= " AND genre = :genre ";
            $binds[":genre"] = $genre;
        }
        $sql .= " ORDER BY created_at DESC LIMIT :offset, :limiter";
        // Bind Values
        $binds[":offset"] = $offset;
        $binds[":limiter"] = $limiter;
        $this->db->queryWithParams($sql, $binds);
        $results = $this->db->resultSet();
        return $results;
    }

    public function getRowCount()
    {
        return $this->db->rowCount();
    }

    public function getBooks()
    {
        $this->db->query("SELECT * FROM items ORDER BY created_at DESC");
        $results = $this->db->resultSet();
        return $results;
    }

    public function getBooksByPagination($offset, $limiter)
    {
        $this->db->query("SELECT * FROM items ORDER BY created_at DESC LIMIT :offset, :limiter");
        // Bind values
        $this->db->bind(":offset", $offset);
        $this->db->bind(":limiter", $limiter);
        $results = $this->db->resultSet();
        $results = json_decode(json_encode($results), FALSE);

        return $results;
    }

    public function getMultipleBooksById($idArray)
    {
        $in = str_repeat('?,', count($idArray) - 1) . '?';
        $this->db->query("SELECT * FROM items WHERE id IN ($in)");
        $results = $this->db->resultSet($idArray);
        return $results;
    }

    public function getBookGenresById($id)
    {
        // TODO 3- Kitap kategorilerinin sonucunu döndüren metodu yazınız.

        $this->db->query("SELECT * FROM genres WHERE id = :id");
        $this->db->bind(":id", $id);
        $row = $this->db->single();
        return $row;
    }

    public function getGenresList()
    {
        $this->db->query("SELECT * FROM genres");
        $results = $this->db->resultSet();
        return $results;
    }

    public function addBook($data)
    {
        $this->db->query("INSERT INTO items (name, description, price, image) VALUES (:name, :description, :price, :image)");
        // Bind values
        $this->db->bind(":name", $data['name']);
        $this->db->bind(":description", $data['description']);
        $this->db->bind(":price", $data['price']);
        $this->db->bind(":image", $data['image']);
        // Execute
        if ($this->db->execute()) {

            // Insert genres of items into items_genres table
            $id = $this->db->getLastInsertId();
            $this->db->query("INSERT INTO items_genres (item_id, genre_id) VALUES (:item_id, :genre_id)");
            $this->db->bind(":item_id", $id);
            foreach ($data['genresChecked'] as $genreKey => $genre) {
                if (!empty($genre)) {
                    $this->db->bind(":genre_id", $genreKey);
                    $this->db->execute();
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function updateBook($data)
    {
        $this->db->query("UPDATE items SET name = :name, description = :description, price = :price, image = :image WHERE id = :id");
        // Bind values
        $this->db->bind(":name", $data['name']);
        $this->db->bind(":description", $data['description']);
        $this->db->bind(":price", $data['price']);
        $this->db->bind(":image", $data['image']);
        $this->db->bind(":id", $data['id']);
        // Execute
        if ($this->db->execute()) {
            // Delete existing genres
            if ($this->deleteBookGenresById($data['id'])) {
                // Insert genres of items into items_genres table
                foreach ($data['genresChecked'] as $genreKey => $genre) {
                    if (!empty($genre)) {
                        $this->db->query("INSERT INTO items_genres (item_id, genre_id) VALUES (:item_id, :genre_id)");
                        $this->db->bind(":item_id", $data['id']);
                        $this->db->bind(":genre_id", $genreKey);
                        $this->db->execute();
                    }
                }
            } else {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function deleteBookGenresById($id)
    {
        $this->db->query("DELETE FROM items_genres WHERE item_id = :id");
        $this->db->bind(":id", $id);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getBookById($id)
    {
        $this->db->query("SELECT * FROM items WHERE id = :id");
        $this->db->bind(":id", $id);

        $row = $this->db->single();

        return $row;
    }

    public function deleteBook($id)
    {
        $this->db->query("DELETE FROM items WHERE id = :id");
        // Bind values
        $this->db->bind(":id", $id);
        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

?>