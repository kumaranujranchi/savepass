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
    /* Premium Notes Layout */
    .notes-container {
        display: flex;
        height: calc(100vh - 180px);
        background: #1e1e1e;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
    }

    .notes-sidebar {
        width: 320px;
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        background: #16161a;
    }

    .notes-sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notes-list {
        flex: 1;
        overflow-y: auto;
    }

    .note-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: all 0.2s;
        display: block;
        text-decoration: none;
    }

    .note-item:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .note-item.active {
        background: rgba(44, 15, 189, 0.1);
        border-left: 3px solid var(--accent-primary);
    }

    .note-item-title {
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 1rem;
    }

    .note-item-preview {
        font-size: 0.8rem;
        color: var(--text-dim);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }

    .notes-content {
        flex: 1;
        background: #1e1e1e;
        display: flex;
        flex-direction: column;
    }

    .notes-placeholder {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--text-dim);
        padding: 2rem;
        text-align: center;
    }

    .editor-header {
        padding: 1.25rem 2rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #1e1e1e;
    }

    .editor-body {
        flex: 1;
        padding: 2rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .note-input-title {
        background: transparent;
        border: none;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        width: 100%;
        outline: none;
        padding: 0;
        margin: 0;
    }

    .note-input-content {
        flex: 1;
        background: transparent;
        border: none;
        font-size: 1.1rem;
        color: var(--text-secondary);
        width: 100%;
        outline: none;
        resize: none;
        line-height: 1.6;
        padding: 0;
        margin: 0;
    }

    .btn-save-note {
        background: var(--accent-primary);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }

    @media (max-width: 1024px) {
        .notes-sidebar {
            width: 100%;
        }

        .notes-list {
            border-right: none;
        }

        .notes-content {
            display: none;
        }

        .notes-content.mobile-show {
            display: flex;
            position: absolute;
            inset: 0;
            z-index: 100;
        }
    }
</style>

<div class="notes-container">
    <div class="notes-sidebar">
        <div class="notes-sidebar-header">
            <span
                style="font-weight: 800; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: var(--text-dim);">My
                Notes</span>
            <a href="notes.php?id=new" class="btn-pro"
                style="padding: 6px 12px; font-size: 0.75rem; text-decoration: none;">+ New Note</a>
        </div>
        <div class="notes-list">
            <?php foreach ($notes_list as $note): ?>
                <a href="notes.php?id=<?php echo $note['id']; ?>"
                    class="note-item <?php echo ($current_note && $current_note['id'] == $note['id']) ? 'active' : ''; ?>">
                    <div class="note-item-title"><?php echo htmlspecialchars($note['title']); ?></div>
                    <div class="note-item-preview">
                        Updated <?php echo formatDate($note['created_at']); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div
        class="notes-content <?php echo ($current_note || (isset($_GET['id']) && $_GET['id'] == 'new')) ? 'mobile-show' : ''; ?>">
        <?php if ($current_note || (isset($_GET['id']) && $_GET['id'] == 'new')): ?>
            <form method="post" action="notes.php" style="height: 100%; display: flex; flex-direction: column;">
                <input type="hidden" name="note_id" value="<?php echo $current_note ? $current_note['id'] : ''; ?>">
                <div class="editor-header">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <a href="notes.php" class="mobile-only"
                            style="font-size: 1.5rem; text-decoration: none; color: var(--text-primary);">←</a>
                        <span
                            style="font-size: 0.8rem; color: var(--accent-secondary); font-weight: 800; text-transform: uppercase;">
                            <?php echo $current_note ? 'Editing Note' : 'New Note'; ?>
                        </span>
                    </div>
                    <button type="submit" class="btn-save-note">Save changes</button>
                </div>
                <div class="editor-body">
                    <input type="text" name="title" class="note-input-title" placeholder="Note Title"
                        value="<?php echo $current_note ? htmlspecialchars($current_note['title']) : ''; ?>" required>
                    <textarea name="content" class="note-input-content" placeholder="Start writing something amazing..."
                        required><?php echo $current_note ? htmlspecialchars(decryptData($current_note['content_enc'])) : ''; ?></textarea>
                </div>
            </form>
        <?php else: ?>
            <div class="notes-placeholder">
                <div style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.5;">📝</div>
                <h2 style="color: var(--text-primary); margin-bottom: 10px;">Select a note to view</h2>
                <p>Choose a note from the list on the left or create a new one to get started.</p>
                <a href="notes.php?id=new" class="btn btn-primary"
                    style="margin-top: 1.5rem; width: auto; padding: 0.75rem 2.5rem;">Create New Note</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>