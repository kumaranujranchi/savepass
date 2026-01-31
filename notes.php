<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];
$current_note = null;
$message = "";

// Handle Save
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = cleanInput($_POST["title"]);
    $content_raw = $_POST["content"];
    $content_enc = encryptData($content_raw);

    if (isset($_POST['note_id']) && !empty($_POST['note_id'])) {
        // Update
        $id = $_POST['note_id'];
        $stmt = $pdo->prepare("UPDATE secure_notes SET title = :title, content_enc = :content WHERE id = :id AND user_id = :uid");
        $stmt->execute([':title' => $title, ':content' => $content_enc, ':id' => $id, ':uid' => $user_id]);
        $message = "Note updated!";
    } else {
        // Create
        $stmt = $pdo->prepare("INSERT INTO secure_notes (user_id, title, content_enc) VALUES (:uid, :title, :content)");
        $stmt->execute([':uid' => $user_id, ':title' => $title, ':content' => $content_enc]);
        $message = "Note created!";
        $id = $pdo->lastInsertId(); // retrieve the new id
    }
    // reload to show correct state
    header("Location: notes.php?id=" . $id);
    exit;
}

// Fetch All Notes List
$stmt = $pdo->prepare("SELECT id, title, created_at FROM secure_notes WHERE user_id = :uid ORDER BY updated_at DESC");
$stmt->execute([':uid' => $user_id]);
$notes_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Current Note
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM secure_notes WHERE id = :id AND user_id = :uid");
    $stmt->execute([':id' => $_GET['id'], ':uid' => $user_id]);
    $current_note = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php require_once "includes/header.php"; ?>

<style>
    /* Override default layout for this page to match 2-column design */
    .notes-container {
        display: flex;
        height: calc(100vh - 100px);
        border: 1px solid #333;
        border-radius: 8px;
        overflow: hidden;
    }

    .notes-list {
        width: 300px;
        background: #181818;
        border-right: 1px solid #333;
        overflow-y: auto;
    }

    .notes-editor {
        flex: 1;
        background: #121212;
        display: flex;
        flex-direction: column;
    }

    .note-item {
        padding: 1rem;
        border-bottom: 1px solid #333;
        display: block;
        transition: background 0.2s;
    }

    .note-item:hover,
    .note-item.active {
        background: #222;
    }

    .note-preview {
        font-size: 0.8rem;
        color: #888;
        margin-top: 0.3rem;
    }

    textarea.editor-textarea {
        flex: 1;
        width: 100%;
        border: none;
        background: transparent;
        color: #ccc;
        resize: none;
        padding: 2rem;
        font-family: monospace;
        font-size: 1rem;
        outline: none;
    }

    input.editor-title {
        width: 100%;
        border: none;
        background: transparent;
        color: white;
        font-size: 1.5rem;
        padding: 2rem 2rem 0;
        outline: none;
        font-weight: bold;
    }
</style>

<div class="notes-container">
    <div class="notes-list">
        <a href="notes.php" class="note-item" style="text-align: center; color: #2c0fbd; font-weight: bold;">+ New
            Note</a>
        <?php foreach ($notes_list as $note): ?>
            <a href="notes.php?id=<?php echo $note['id']; ?>"
                class="note-item <?php echo ($current_note && $current_note['id'] == $note['id']) ? 'active' : ''; ?>">
                <div style="font-weight: bold;">
                    <?php echo htmlspecialchars($note['title']); ?>
                </div>
                <div class="note-preview">
                    <?php echo formatDate($note['created_at']); ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="notes-editor">
        <form method="post" style="display: flex; flex-direction: column; height: 100%;">
            <input type="hidden" name="note_id" value="<?php echo $current_note ? $current_note['id'] : ''; ?>">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-right: 2rem;">
                <input type="text" name="title" class="editor-title" placeholder="Note Title"
                    value="<?php echo $current_note ? htmlspecialchars($current_note['title']) : ''; ?>" required>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            <textarea name="content" class="editor-textarea"
                placeholder="Start typing your secure note..."><?php echo $current_note ? htmlspecialchars(decryptData($current_note['content_enc'])) : ''; ?></textarea>
        </form>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>