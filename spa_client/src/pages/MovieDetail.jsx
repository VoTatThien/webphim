import React, { useState, useEffect } from 'react';
import { getMovieDetail } from '../services/api';

const MovieDetail = ({ movieId, onSelectSlot, onBack }) => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [selectedTheatreId, setSelectedTheatreId] = useState(null);
  const [selectedDate, setSelectedDate] = useState(null);

  useEffect(() => {
    const fetchDetail = async () => {
      setLoading(true);
      const detail = await getMovieDetail(movieId);
      setData(detail);
      
      // Auto-select first theatre and first date if available
      if (detail && detail.theatres && detail.theatres.length > 0) {
        setSelectedTheatreId(detail.theatres[0].id_rap);
        if (detail.theatres[0].lich_dien_ra && detail.theatres[0].lich_dien_ra.length > 0) {
          setSelectedDate(detail.theatres[0].lich_dien_ra[0].ngay);
        }
      }
      setLoading(false);
    };
    fetchDetail();
  }, [movieId]);

  if (loading) {
    return (
      <div style={{ display: 'flex', justifyContent: 'center', padding: '100px 0' }}>
        <div style={{
          border: '4px solid rgba(255, 255, 255, 0.1)',
          borderTop: '4px solid #8b5cf6',
          borderRadius: '50%',
          width: '40px',
          height: '40px',
          animation: 'spin 1s linear infinite'
        }}></div>
      </div>
    );
  }

  if (!data || !data.movie) {
    return (
      <div style={{ textAlign: 'center', padding: '60px 6%' }}>
        <h3 style={{ color: '#ef4444' }}>Không tìm thấy thông tin phim.</h3>
        <button className="btn btn-secondary" style={{ marginTop: '20px' }} onClick={onBack}>
          Quay lại Trang chủ
        </button>
      </div>
    );
  }

  const { movie, theatres } = data;

  // Find currently selected theatre
  const currentTheatre = theatres.find(t => t.id_rap === selectedTheatreId);
  
  // Get unique dates for the selected theatre
  const dates = currentTheatre ? currentTheatre.lich_dien_ra : [];
  
  // Find currently selected date details
  const currentDateDetails = dates.find(d => d.ngay === selectedDate);
  
  // Get showtime slots for the selected date
  const showtimes = currentDateDetails ? currentDateDetails.suat_chieu : [];

  // Helper to format date display
  const formatDateDisplay = (dateString) => {
    const date = new Date(dateString);
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const weekdays = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
    const weekday = weekdays[date.getDay()];
    return { weekday, label: `${day}/${month}` };
  };

  return (
    <div className="detail-container animate-fade-in">
      <button className="btn btn-secondary" style={{ marginBottom: '30px' }} onClick={onBack}>
        ⬅️ Quay Lại
      </button>

      {/* Movie Details Header */}
      <div className="detail-header glass-card">
        <img 
          src={movie.img_url || 'https://via.placeholder.com/300x450'} 
          alt={movie.tieu_de} 
          className="detail-poster"
          onError={(e) => {
            e.target.onerror = null; 
            e.target.src = 'https://via.placeholder.com/300x450?text=No+Poster';
          }}
        />
        <div className="detail-info">
          {movie.gia_han_tuoi && (
            <span className="detail-genre" style={{ background: '#ef4444', color: '#fff' }}>
              Cấm trẻ em dưới {movie.gia_han_tuoi} tuổi (T{movie.gia_han_tuoi})
            </span>
          )}
          <h1 className="detail-title">{movie.tieu_de}</h1>
          <span className="detail-genre">{movie.genre}</span>
          <p className="detail-desc">{movie.mo_ta || 'Chưa có mô tả tóm tắt cho bộ phim này.'}</p>
          
          <div className="detail-grid">
            <div className="detail-item">
              <strong>Đạo diễn</strong>
              <span>{movie.daodien || 'N/A'}</span>
            </div>
            <div className="detail-item">
              <strong>Diễn viên</strong>
              <span>{movie.dienvien || 'N/A'}</span>
            </div>
            <div className="detail-item">
              <strong>Thời lượng</strong>
              <span>⏱️ {movie.thoi_luong_phim} phút</span>
            </div>
            <div className="detail-item">
              <strong>Quốc gia</strong>
              <span>🌐 {movie.quoc_gia || 'N/A'}</span>
            </div>
          </div>
        </div>
      </div>

      {/* Showtimes Selector section */}
      <div className="showtimes-box glass-card">
        <h3 style={{ fontSize: '22px', fontWeight: '700', marginBottom: '24px' }}>📅 Lịch Chiếu & Đặt Vé</h3>

        {theatres.length === 0 ? (
          <p style={{ color: '#a7a3c4', textAlign: 'center', padding: '20px 0' }}>
            Hiện tại chưa có lịch chiếu cho bộ phim này.
          </p>
        ) : (
          <div>
            {/* 1. Theatre Selector */}
            <div style={{ marginBottom: '24px' }}>
              <strong style={{ display: 'block', fontSize: '13px', textTransform: 'uppercase', color: '#a7a3c4', marginBottom: '10px' }}>
                Chọn Rạp Chiếu
              </strong>
              <div style={{ display: 'flex', flexWrap: 'wrap', gap: '10px' }}>
                {theatres.map(t => (
                  <button
                    key={t.id_rap}
                    className={`date-btn ${selectedTheatreId === t.id_rap ? 'active' : ''}`}
                    onClick={() => {
                      setSelectedTheatreId(t.id_rap);
                      if (t.lich_dien_ra && t.lich_dien_ra.length > 0) {
                        setSelectedDate(t.lich_dien_ra[0].ngay);
                      } else {
                        setSelectedDate(null);
                      }
                    }}
                  >
                    🏢 {t.ten_rap}
                  </button>
                ))}
              </div>
            </div>

            {/* Address display */}
            {currentTheatre && (
              <div style={{ padding: '12px 16px', background: 'rgba(255, 255, 255, 0.03)', borderRadius: '10px', fontSize: '13px', color: '#a7a3c4', marginBottom: '24px', border: '1px solid rgba(255, 255, 255, 0.05)' }}>
                📍 <strong>Địa chỉ:</strong> {currentTheatre.dia_chi}
              </div>
            )}

            {/* 2. Date Selector */}
            {dates.length > 0 && (
              <div style={{ marginBottom: '24px' }}>
                <strong style={{ display: 'block', fontSize: '13px', textTransform: 'uppercase', color: '#a7a3c4', marginBottom: '10px' }}>
                  Chọn Ngày Chiếu
                </strong>
                <div className="date-selector">
                  {dates.map(d => {
                    const formatted = formatDateDisplay(d.ngay);
                    return (
                      <button
                        key={d.ngay}
                        className={`date-btn ${selectedDate === d.ngay ? 'active' : ''}`}
                        onClick={() => setSelectedDate(d.ngay)}
                        style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', gap: '2px', padding: '10px 20px' }}
                      >
                        <span style={{ fontSize: '11px', opacity: 0.8 }}>{formatted.weekday}</span>
                        <span style={{ fontSize: '15px', fontWeight: '700' }}>{formatted.label}</span>
                      </button>
                    );
                  })}
                </div>
              </div>
            )}

            {/* 3. Showtime Slots Selector */}
            {selectedDate && (
              <div>
                <strong style={{ display: 'block', fontSize: '13px', textTransform: 'uppercase', color: '#a7a3c4', marginBottom: '12px' }}>
                  Chọn Suất Chiếu (Khung Giờ)
                </strong>
                
                {showtimes.length === 0 ? (
                  <p style={{ color: '#a7a3c4', fontSize: '14px' }}>Không có suất chiếu nào cho ngày này.</p>
                ) : (
                  <div className="slots-grid">
                    {showtimes.map(slot => (
                      <button
                        key={slot.id_slot}
                        className="slot-btn"
                        onClick={() => onSelectSlot(slot.id_slot)}
                      >
                        <span className="slot-time">⏰ {slot.thoi_gian_chieu.substring(0, 5)}</span>
                        <span style={{ fontSize: '11px', color: '#3b82f6', fontWeight: '600' }}>{slot.ten_phong}</span>
                        <span className="slot-seats">🪑 {slot.ghe_trong} ghế trống</span>
                      </button>
                    ))}
                  </div>
                )}
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default MovieDetail;
