--Liệt kê các phòng còn trống;

SELECT SoPhong, Loai, GiaThue, TrangThaiThue
FROM PHONG
WHERE TrangThaiThue = N'Trong';
