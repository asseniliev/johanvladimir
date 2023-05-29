CREATE TABLE mod_faq_questions (
  id int(10) NOT NULL PRIMARY KEY,
  owner varchar(20),
  editor varchar(20),
  ip text,
  label text,
  groups mediumtext,
  created int(11),
  updated int(11),
  hidden int(1),
  approved int(1),
  answer text,
  hits int(10),
  numScores int(10),
  avgScore double(1,2),
  totalScores int(10),
  compScore double(2,1),
  contact varchar(100)
);

CREATE TABLE mod_faq_settings (
  anon int(1) DEFAULT 1,
  comments int(1) DEFAULT 1,
  suggestions int(1) DEFAULT 1,
  layout_view int(1) DEFAULT 0,
  use_bookmarks int(1) DEFAULT 0,
  score_text text,
  paging_limit int(10) DEFAULT 5,
  sorting_method int(10) DEFAULT 0,
  custom_top_bullet varchar(3) DEFAULT NULL,
  custom_sub_bullet varchar(3) DEFAULT NULL,
  default_bullets int(1) DEFAULT 1,
  cat_show_updated int(1) DEFAULT 1,
  cat_show_num_faqs int(1) DEFAULT 1
);
