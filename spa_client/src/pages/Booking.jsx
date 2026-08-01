import React, { useState, useEffect } from 'react';
import { getSeatsForSlot } from '../services/api';

const Booking = ({ slotId, onBack }) => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [selectedSeats, setSelectedSeats] = useState([]);
  const [bookingSuccess, setBookingSuccess] = useState(false);

  useEffect(() => {
    const fetchSeats = async () => {
      setLoading(true);
      const seatsData = await getSeatsForSlot(slotId);
      setData(seatsData);
      setSelectedSeats([]);
      setBookingSuccess(false);
      setLoading(false);
    };
    fetchSeats();
  }, [slotId]);

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

  if (!data) {
    return (
      <div style={{ textAlign: 'center', padding: '60px 6%' }}>
        <h3 style={{ color: '#ef4444' }}>Không thể tải sơ đồ ghế ngồi.</h3>
        <button className="btn btn-secondary" style={{ marginTop: '20px' }} onClick={onBack}>
          Quay lại Lịch chiếu
        </button>
      </div>
    );
  }

  const { slot_info, seats, booked_seats } = data;

  // Group seats by row for rendering
  const seatsByRow = {};
  seats.forEach(seat => {
    if (!seatsByRow[seat.row_label]) {
      seatsByRow[seat.row_label] = [];
    }
    seatsByRow[seat.row_label].push(seat);
  });

  // Prices configuration
  const prices = {
    cheap: 60000,
    middle: 80000,
    expensive: 100000
  };

  const handleSeatClick = (seatCode, active, booked, tier) => {
    if (!active || booked) return;

    if (selectedSeats.includes(seatCode)) {
      setSelectedSeats(selectedSeats.filter(code => code !== seatCode));
    } else {
      setSelectedSeats([...selectedSeats, seatCode]);
    }
  };

  // Calculate total price
  const calculateTotal = () => {
    return selectedSeats.reduce((total, seatCode) => {
      const seat = seats.find(s => s.code === seatCode);
      const price = seat ? prices[seat.tier] : 0;
      return total + price;
    }, 0);
  };

  const handleConfirmBooking = () => {
    if (selectedSeats.length === 0) {
      alert('Vui lòng chọn ít nhất 1 ghế ngồi');
      return;
    }
    setBookingSuccess(true);
  };

  return (
    <div className="detail-container animate-fade-in" style={{ maxWidth: '1000px', margin: '0 auto' }}>
      <button className="btn btn-secondary" style={{ marginBottom: '20px' }} onClick={onBack}>
        ⬅️ Quay Lại Lịch Chiếu
      </button>

      {bookingSuccess ? (
        /* Booking Confirmation Mock Modal */
        <div className="glass-card text-center animate-fade-in" style={{ padding: '40px', textAlign: 'center' }}>
          <div style={{ fontSize: '70px', marginBottom: '20px' }}>🎉</div>
          <h2 style={{ fontSize: '28px', color: '#e2b659', marginBottom: '10px' }}>Đăng Ký Đặt Vé Thành Công!</h2>
          <p style={{ color: '#a7a3c4', marginBottom: '24px' }}>
            Bạn đã đặt thành công các ghế: <strong style={{ color: '#fff' }}>{selectedSeats.join(', ')}</strong> tại <strong>{slot_info.ten_phong}</strong>.
          </p>

          {/* Simulated VietQR Checkout Code */}
          <div style={{ background: '#fff', padding: '16px', borderRadius: '16px', display: 'inline-block', marginBottom: '24px', boxShadow: '0 10px 25px rgba(0,0,0,0.3)' }}>
            <img 
              src={`https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(`Thanh toan ve CinePass: ${selectedSeats.join(',')}`)}`} 
              alt="Checkout QR" 
              style={{ display: 'block', width: '180px', height: '180px' }}
            />
            <div style={{ color: '#0f172a', fontWeight: '800', marginTop: '10px', fontSize: '15px' }}>
              Trị giá: {calculateTotal().toLocaleString('vi-VN')}đ
            </div>
            <div style={{ color: '#475569', fontSize: '10px', marginTop: '2px' }}>Quét mã VietQR để thanh toán giả lập</div>
          </div>

          <div>
            <button className="btn btn-primary" onClick={onBack}>
              Quay lại Trang chủ
            </button>
          </div>
        </div>
      ) : (
        /* Seat Booking Interface */
        <div className="glass-card">
          {/* Header detail */}
          <div style={{ borderBottom: '1px solid rgba(255, 255, 255, 0.08)', paddingBottom: '16px', marginBottom: '24px' }}>
            <h2 style={{ fontSize: '24px', fontWeight: '800', color: '#e2b659' }}>{slot_info.ten_phim}</h2>
            <p style={{ color: '#a7a3c4', fontSize: '14px', marginTop: '4px' }}>
              🏢 {slot_info.ten_phong} ({slot_info.loai_phong}) | ⏰ Suất: <strong>{slot_info.thoi_gian_chieu.substring(0, 5)}</strong> ngày {new Date(slot_info.ngay_chieu).toLocaleDateString('vi-VN')}
            </p>
          </div>

          <div className="seatmap-container">
            {/* Legend */}
            <div className="seat-legend">
              <div className="legend-item">
                <span className="seat active cheap" style={{ cursor: 'default' }}></span>
                <span>Thường (60k)</span>
              </div>
              <div className="legend-item">
                <span className="seat active middle" style={{ cursor: 'default' }}></span>
                <span>Thương gia (80k)</span>
              </div>
              <div className="legend-item">
                <span className="seat active expensive" style={{ cursor: 'default' }}></span>
                <span>VIP (100k)</span>
              </div>
              <div className="legend-item">
                <span className="seat selected" style={{ cursor: 'default' }}></span>
                <span>Đang chọn</span>
              </div>
              <div className="legend-item">
                <span className="seat booked" style={{ cursor: 'default' }}>X</span>
                <span>Đã đặt</span>
              </div>
            </div>

            {/* Cinema Screen */}
            <div className="cinema-screen"></div>

            {/* Layout Grid */}
            <div className="seats-grid-wrapper">
              <div className="seats-layout">
                {Object.keys(seatsByRow).sort().map(rowLabel => (
                  <div key={rowLabel} className="seat-row">
                    <span className="row-header">{rowLabel}</span>
                    {seatsByRow[rowLabel].map(seat => {
                      const isBooked = booked_seats.includes(seat.code);
                      const isSelected = selectedSeats.includes(seat.code);
                      
                      let seatClass = 'active';
                      if (isBooked) seatClass = 'booked';
                      else if (isSelected) seatClass = 'selected';
                      else seatClass = `active ${seat.tier}`;

                      return (
                        <div
                          key={seat.code}
                          className={`seat ${seatClass}`}
                          onClick={() => handleSeatClick(seat.code, seat.active, isBooked, seat.tier)}
                          title={`${seat.code} - ${seat.tier === 'cheap' ? 'Thường' : seat.tier === 'middle' ? 'Thương gia' : 'VIP'}`}
                        >
                          {isBooked ? 'X' : seat.seat_number}
                        </div>
                      );
                    })}
                    <span className="row-header">{rowLabel}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Checkout Footer Bar */}
          <div style={{ borderTop: '1px solid rgba(255, 255, 255, 0.08)', paddingTop: '20px', marginTop: '30px' }}>
            <div className="booking-checkout-card">
              <div className="checkout-info">
                <span className="checkout-label">Ghế đã chọn:</span>
                <span style={{ fontSize: '15px', fontWeight: '700' }}>
                  {selectedSeats.length > 0 ? selectedSeats.join(', ') : 'Chưa chọn ghế nào'}
                </span>
              </div>

              <div className="checkout-info">
                <span className="checkout-label">Tổng tiền vé:</span>
                <span className="checkout-value">{calculateTotal().toLocaleString('vi-VN')} đ</span>
              </div>

              <button 
                className="btn btn-gold" 
                disabled={selectedSeats.length === 0}
                onClick={handleConfirmBooking}
                style={{ padding: '14px 36px', fontSize: '15px' }}
              >
                🎟️ Đặt Vé & Thanh Toán
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Booking;
