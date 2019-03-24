-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-- TODO: create tables

-- Users Table
CREATE TABLE 'users' (
    'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'username' TEXT NOT NULL UNIQUE,
    'password' TEXT NOT NULL
);

--Users table seed data
-- TODO: FOR HASHED PASSWORDS, LEAVE A COMMENT WITH THE PLAIN TEXT PASSWORD!
INSERT INTO 'users' (id, username, password) VALUES (1, "lesKnope", 'waffles');
-- Username: lesKnope, Password: waffles

INSERT INTO 'users' (id, username, password) VALUES (2, "tommyH", 'treatYOSELF');
-- Username: tommyH, Password: treatYOSELF

-- Sessions Table
CREATE TABLE 'sessions' (
    'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'user_id' INTEGER NOT NULL,
    'session' TEXT NOT NULL UNIQUE
);

-- Images Table
CREATE TABLE 'images' (
    'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'citation' TEXT,
    'user_id' INTEGER NOT NULL
);

--Images table seed data
INSERT INTO 'images' (id, citation, user_id) VALUES (1, "PromGirl: https://img.promgirl.com/_img/PGPRODUCTS/1770252/320/light-pink-dress-JO-JVN-JVN55885-a.jpg", 1); --fashion, prom

INSERT INTO 'images' (id, citation, user_id) VALUES (2, "PromGirl: https://sep.yimg.com/ay/yhst-130634544928068/clarisse-a-line-prom-dress-3000-4-colors-61.jpg", 1); --fashion, prom

INSERT INTO 'images' (id, citation, user_id) VALUES (3, "Lulus: https://www.lulus.com/video/so_3/product/390882.jpg", 1); --fashion, prom, wedding

INSERT INTO 'images' (id, citation, user_id) VALUES (4, "Tara Florence Artistry: https://static1.squarespace.com/static/56bb0cc9746fb9d5209317f8/5703cdb37c65e42063febbf2/582399a74402431a68991274/1489321487743/IMG_0760.JPG", 2); --makeup

INSERT INTO 'images' (id, citation, user_id) VALUES (5, "Cadence & Eli Photography: https://assets.marthastewartweddings.com/styles/wmax-520-highdpi/d66/warehouse-wedding-venue-the-aria-los-angeles-california-intro-0815/warehouse-wedding-venue-the-aria-los-angeles-california-intro-0815_vert.jpg?itok=-N-32u77", 2); --venue

INSERT INTO 'images' (id, citation, user_id) VALUES (6, "Samm Blake: https://assets.marthastewartweddings.com/styles/wmax-520-highdpi/d40/eden-jack-wedding-tent-0849-6427698-1117/eden-jack-wedding-tent-0849-6427698-1117_vert.jpg?itok=-yhGvBkP", 2); --decorations

INSERT INTO 'images' (id, citation, user_id) VALUES (7, "Christian Oth Studio: https://assets.marthastewartweddings.com/styles/wmax-520-highdpi/d36/tent-decor-christina-oth-studio-0118/tent-decor-christina-oth-studio-0118_vert.jpg?itok=XbbUZxnn", 2); --decorations

INSERT INTO 'images' (id, citation, user_id) VALUES (8, "Kleinfeld: https://www.kleinfeldbridal.com/wp-content/uploads/2018/01/maggie-sottero-simple-a-line-wedding-dress-33726795.jpg", 2); --fashion

INSERT INTO 'images' (id, citation, user_id) VALUES (9, "David's Bridal: https://img.davidsbridal.com/is/image/DavidsBridalInc/VW351307_IVORY_VW_S16PROD?$plpproductimgmobile_1up$", 2); --fashion

INSERT INTO 'images' (id, citation, user_id) VALUES (10, "Kleinfeld: https://www.kleinfeldbridal.com/wp-content/uploads/2018/04/allison-webb-fit-and-flare-wedding-dress-with-lace-bodice-and-v-neckline-33741174.jpg", 2); --fashion

-- Tags Table
CREATE TABLE 'tags' (
    'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'tag' TEXT NOT NULL UNIQUE,
);

--Tags table seed data
INSERT INTO 'tags' (id, tag) VALUES (1, 'fashion');
INSERT INTO 'tags' (id, tag) VALUES (2, 'prom');
INSERT INTO 'tags' (id, tag) VALUES (3, 'wedding');
INSERT INTO 'tags' (id, tag) VALUES (4, 'makeup');
INSERT INTO 'tags' (id, tag) VALUES (5, 'venue');
INSERT INTO 'tags' (id, tag) VALUES (6, 'decorations');

-- image_tags Table
CREATE TABLE 'image_tags' (
    'id' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'image_id' INTEGER NOT NULL,
    'tag_id' INTEGER NOT NULL,
);

--Tags table seed data
INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (1, 1, 1);
INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (2, 1, 2);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (3, 2, 1);
INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (4, 2, 2);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (5, 3, 1);
INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (6, 3, 2);
INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (7, 3, 3);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (8, 4, 4);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (9, 5, 5);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (10, 6, 6);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (11, 7, 6);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (12, 8, 1);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (13, 9, 1);

INSERT INTO 'image_tags' (id, image_id, tag_id) VALUES (14, 10, 1);


COMMIT;
