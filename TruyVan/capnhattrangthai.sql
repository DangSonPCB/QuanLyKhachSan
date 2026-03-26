--Cập nhật trạng thái phòng khi khách check-in (Đổi Trống thành Đã thuê)
UPDATE 
  
PHONG SET TrangThaiThue = N'Đã thuê' 
WHERE SoPhong = 'P102'; 
