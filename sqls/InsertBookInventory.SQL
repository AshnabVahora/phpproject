USE BookStore;

INSERT INTO Categories
  (CategoryID, CategoryDescription)
  VALUES 
  (1, 'Fiction'),
  (2, 'Non-Fiction'),
  (3, 'Biography'),
  (4, "History");

INSERT INTO PaymentOptions
  (PaymentOptionID, PaymentOptionName)
  VALUES 
  (1, 'VISA'),
  (2, 'MASTER'),
  (3, 'DEBIT');


INSERT INTO BookInventories
  (BookName, BookImgUrl, 
   BookAuthor, RetailPrice, Quantity, CategoryID)
  VALUES 
  ('The 13th hour Chaos', "1.jpg", "Doetsch Richard", 10.2, 2, 1),
  ('Across The Sand', "2.jpg", "Howey Hugh", 8.2, 7, 1),
  ('AAll the seas of the world', "3.jpg", "Guy Gavriel Kay", 7.9, 6, 1),
  ('Alone in the wild', "4.jpg", "Kelley Armstrong", 6.3, 4, 1),

  ('101 Essays that Will Change the Way You Think', "5.jpg", "Brianna Wiest", 18.2, 6, 2),
  ('Backroads of Route 66', "6.jpg", "Jim Hinckley", 13.9, 2, 2),
  ('Bath Massacre, New Edition', "7.jpg", "Arnie Bernstein", 16.1, 12, 2),
  ('Black ops : the life of a CIA shadow warrior', "8.jpg", " Ric Prado", 9.9, 8, 2),

  ('Cristiano Ronaldo the biography', "9.jpg", "Guillem Balague", 18.8, 4, 3),
  ('Shark: the biography of Greg Norman', "10.jpg", "Lauren St. John", 17.8, 5, 3),
  ('Phil Collins: the definitive biography', "11.jpg", "Ray Coleman", 15.3, 7, 3),
  ('Simenon: a biography / Pierre Assouline', "12.jpg", "Jon Rothschild", 7.2, 6, 3),

  ('Chasing history : a kid in the newsroom', "13.jpg", "Carl Bernstein", 19.2, 8, 4),
  ('Worn: A People History of Clothing', "14.jpg", "Sofi Thanhauser", 25.2, 7, 4),
  ('A brief history of equality', "15.jpg", "Steven Rendall", 15.6, 5, 4),
  ('Yesterday is history', "16.jpg", "Kosoko Jackson", 17.9, 17, 4);

  
  
