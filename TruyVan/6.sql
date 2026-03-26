--Liệt kê số phòng có giá dưới 1.000.000.
  
SELECT SoPhong, Loai, GiaThue, TrangThaiThue
FROM PHONG
WHERE GiaThue < 1000000;
