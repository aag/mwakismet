CREATE TABLE mw_akismet_edits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp VARCHAR(14),
    page_id int(8) unsigned,
    rev_id int(8) unsigned,
    username VARCHAR(64),
    approval enum('spam', 'notspam'),
    content mediumblob,
    akismet_submit_diff mediumblob,
    html_diff longblob
)
