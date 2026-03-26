--Liệt kê số phòng theo loại phòng và còn trống.
SELECT Loai, COUNT(*) AS SoPhongTrong
FROM PHONG
WHERE TrangThaiThue = N'Trong'
GROUP BY Loai;
