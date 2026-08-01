import React, { useState } from 'react';
import Home from './pages/Home';
import MovieDetail from './pages/MovieDetail';
import Booking from './pages/Booking';

function App() {
  const [currentPage, setCurrentPage] = useState('home'); // 'home', 'detail', 'booking'
  const [selectedMovieId, setSelectedMovieId] = useState(null);
  const [selectedSlotId, setSelectedSlotId] = useState(null);

  const handleSelectMovie = (movieId) => {
    setSelectedMovieId(movieId);
    setCurrentPage('detail');
  };

  const handleSelectSlot = (slotId) => {
    setSelectedSlotId(slotId);
    setCurrentPage('booking');
  };

  const handleGoHome = () => {
    setSelectedMovieId(null);
    setSelectedSlotId(null);
    setCurrentPage('home');
  };

  const handleBackToDetail = () => {
    setSelectedSlotId(null);
    setCurrentPage('detail');
  };

  return (
    <div>
      {/* Header Navbar */}
      <header className="navbar">
        <div className="logo" onClick={handleGoHome}>
          🎬 CinePass<span>SPA</span>
        </div>
        <nav className="nav-links">
          <span 
            className={`nav-link ${currentPage === 'home' ? 'active' : ''}`}
            onClick={handleGoHome}
          >
            Trang Chủ
          </span>
          <span 
            className="nav-link"
            onClick={() => window.open('http://localhost:83/webphim_hung/Trang-nguoi-dung/', '_blank')}
          >
            Giao Diện PHP Gốc 🌐
          </span>
        </nav>
      </header>

      {/* Main Content Area */}
      <main style={{ minHeight: 'calc(100vh - 180px)' }}>
        {currentPage === 'home' && (
          <Home onSelectMovie={handleSelectMovie} />
        )}
        
        {currentPage === 'detail' && (
          <MovieDetail 
            movieId={selectedMovieId} 
            onSelectSlot={handleSelectSlot} 
            onBack={handleGoHome}
          />
        )}
        
        {currentPage === 'booking' && (
          <Booking 
            slotId={selectedSlotId} 
            onBack={handleBackToDetail}
          />
        )}
      </main>

      {/* Footer */}
      <footer style={{
        padding: '30px 6%',
        borderTop: '1px solid rgba(255, 255, 255, 0.08)',
        background: '#090415',
        textAlign: 'center',
        fontSize: '13px',
        color: '#a7a3c4',
        marginTop: '60px'
      }}>
        <p>© 2026 CinePass Cinema Management. Phiên bản nâng cấp Nguyên mẫu SPA (ReactJS + REST API).</p>
        <p style={{ fontSize: '11px', marginTop: '6px', opacity: 0.8 }}>
          Được thiết kế để tăng tốc trải nghiệm, tối ưu hóa database, và hạn chế tải lại trang.
        </p>
      </footer>
    </div>
  );
}

export default App;
