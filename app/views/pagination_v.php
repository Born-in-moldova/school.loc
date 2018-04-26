<?
    $prev = $page > 1 ? $page - 1 : 1;
    $next = $page < $cnt_pages ? $page + 1 : $cnt_pages;
?>

<div style="text-align: center; <?=$cnt_pages < 2 ? 'display: none' : '' ?> " >
    <a href="<?=$length != 5 ? '/?pp=' . $length : '/'?>" style="pointer-events: <?=$page == 1 ? 'none' : 'auto'?>">
        [<<]
    </a>   
    <a href="<?=$prev == 1 ? ($length == 5 ? '/' : '/?pp=' . $length) : ($length == 5 ? '/?page=' . $prev : '/?page=' . $prev . '&pp=' . $length)?>" style="pointer-events: <?=$page == 1 ? 'none' : 'auto'?>">
        <span style="padding-right: 10px"> [<] </span>
    </a>
<?
    for($i = 1; $i <= $cnt_pages; $i++){
        $current_page = '';
        if($page == $i)
            $current_page = 'font: bolder 20px serif; color: #8B0000';
?>
    <a href="<?=$i == 1 ? ($length == 5 ? '/' : '/?pp=' . $length) : ($length == 5 ? '/?page=' . $i : '/?page=' . $i . '&pp=' . $length)?>">
        <span style="padding-right: 10px; <?=$current_page?>">[ <?=$i?> ]</span>
    </a>
<?
    }
?>
    <a href="/?page=<?=$length == 5 ? $next : $next . '&pp=' . $length?>" style="pointer-events: <?=$page == $cnt_pages ? 'none' : 'auto'?>">
        [>]
    </a>
    <a href="/?page=<?=$length == 5 ? ($i-1) : ($i-1) . '&pp=' . $length?>" style="pointer-events: <?=$page == $cnt_pages ? 'none' : 'auto'?>">
        [>>]
    </a>
</div>