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
    /* Responsive Notes Layout */
    .notes-container {
        display: flex;
        height: calc(100vh - 120px);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        background: var(--bg-card);
    }

    .notes-list {
        width: 100%;
        /* Default mobile: full width */
        background: #181818;
        border-right: 1px solid var(--border);
        overflow-y: auto;
        display:
            <?php echo $current_note ? 'none' : 'block'; ?>
        ;
        /* Hide list if note selected on mobile */
    }

    .notes-editor {
        flex: 1;
        background: var(--bg-dark);
        display:
            <?php echo $current_note ? 'flex' : 'none'; ?>
        ;
        /* Show editor if note selected on mobile */
        flex-direction: column;
    }

    /* Desktop Transitions */
    @media (min-width: 1024px) {
        .notes-list {
            width: 300px;
            display: block !important;
        }

        .notes-editor {
            display: flex !important;
        }

        .mobile-back-btn {
            display: none !important;
        }
    }

    .note-item {
        padding: 1.2rem;
        border-bottom: 1px solid var(--border);
        display: block;
        transition: background 0.2s;
    }

    .note-item:hover,
    .note-item.active {
        background: #252525;
    }

    .note-preview {
        font-size: 0.75rem;
        color: var(--text-gray);
        margin-top: 0.4rem;
    }

    textarea.editor-textarea {
        flex: 1;
        width: 100%;
        border: none;
        background: transparent;
        color: var(--text-light);
        resize: none;
        padding: 1.5rem;
        font-family: inherit;
        font-size: 1rem;
        outline: none;
        line-height: 1.6;
    }

    .editor-header {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }

    input.editor-title {
        flex: 1;
        border: none;
        background: transparent;
        color: white;
        font-size: 1.2rem;
        padding: 0.5rem;
        outline: none;
        font-weight: bold;
    }

    .mobile-back-btn {
        padding: 0.5rem;
        margin-right: 0.5rem;
        cursor: pointer;
        font-size: 1.2rem;
    }
</style>

<div class="notes-container">
    <div class="notes-list">
        <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
            <a href="notes.php?id=new" class="btn btn-primary" style="margin: 0;">+ New Note</a>
        </div>
        <?php foreach ($notes_list as $note): ?>
            <a href="notes.php?id=<?php echo $note['id']; ?>"
                class="note-item <?php echo ($current_note && $current_note['id'] == $note['id']) ? 'active' : ''; ?>">
                <div style="font-weight: 600;">
                    <?php echo htmlspecialchars($note['title']); ?>
                </div>
                <div class="note-preview">
                    <?php echo formatDate($note['created_at']); ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($current_note || (isset($_GET['id']) && $_GET['id'] == 'new')): ?>
        <div class="notes-editor">
            <form method="post" style="display: flex; flex-direction: column; height: 100%;">
                <input type="hidden" name="note_id" value="<?php echo $current_note ? $current_note['id'] : ''; ?>">

                <div class="editor-header">
                    <a href="notes.php" class="mobile-back-btn mobile-only">←</a>
                    <input type="text" name="title" class="editor-title" placeholder="Note Title"
                        value="<?php echo $current_note ? htmlspecialchars($current_note['title']) : ''; ?>" required>
                    <button type="submit" class="btn btn-primary"
                        style="width: auto; margin: 0; padding: 0.6rem 1.2rem;">Save</button>
                </div>

                <textarea name="content" class="editor-textarea"
                    placeholder="Start typing your secure note..."><?php echo $current_note ? htmlspecialchars(decryptData($current_note['content_enc'])) : ''; ?></textarea>
            </form>
        </div>
    <?php else: ?>
        <div class="notes-editor desktop-only"
            style="justify-content: center; align-items: center; color: var(--text-gray);">
            <div style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
                <p>Select a note to view or create a new one</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>