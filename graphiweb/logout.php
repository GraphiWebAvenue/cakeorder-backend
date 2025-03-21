<?php
session_start();

// حذف تمام اطلاعات سشن و خروج کاربر
session_unset();
session_destroy();

// هدایت به صفحه ورود یا صفحه اصلی
header("Location: ../index.php");
exit();
