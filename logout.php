<?php
// logout.php
// ============================================================
// Logout Handler
// This file has NO HTML — it just destroys the session
// and sends the user back to the homepage.
//
// How PHP sessions work:
//   session_start()   — resumes the existing session
//   $_SESSION = []    — clears all session variables
//   session_destroy() — deletes the session from the server
// ============================================================

session_start();

// Clear every variable stored in the session
$_SESSION = [];

// Destroy the session itself on the server
session_destroy();

// Send the user back to the landing page
header("Location: index.php");
exit;
