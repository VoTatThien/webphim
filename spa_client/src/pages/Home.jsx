import React, { useState, useEffect } from 'react';
import { getMovies } from '../services/api';

const Home = ({ onSelectMovie }) => {
  const [movies, setMovies] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');

  useEffect(() => {
    const fetchMoviesData = async () => {
      setLoading(true);
      const data = await getMovies();
      setMovies(data);
      setLoading(false);
    };
    fetchMoviesData();
  }, []);

  const filteredMovies = movies.filter(movie => 
    movie.tieu_de.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="showcase-section animate-fade-in">
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '20px', marginBottom: '30px' }}>
        <h2 className="section-title" style={{ marginBottom: 0 }}>Phim Đang Chiếu</h2>
        
        {/* Search bar */}
        <input 
          type="text" 
          placeholder="Tìm kiếm phim..." 
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          style={{
            background: 'rgba(255, 255, 255, 0.05)',
            border: '1px solid rgba(255, 255, 255, 0.1)',
            borderRadius: '12px',
            padding: '12px 20px',
            color: '#fff',
            fontSize: '14px',
            width: '100%',
            maxWidth: '300px',
            outline: 'none',
            transition: 'border-color 0.3s'
          }}
          onFocus={(e) => e.target.style.borderColor = '#8b5cf6'}
          onBlur={(e) => e.target.style.borderColor = 'rgba(255, 255, 255, 0.1)'}
        />
      </div>

      {loading ? (
        <div style={{ display: 'flex', justifyContent: 'center', padding: '60px 0' }}>
          <div style={{
            border: '4px solid rgba(255, 255, 255, 0.1)',
            borderTop: '4px solid #8b5cf6',
            borderRadius: '50%',
            width: '40px',
            height: '40px',
            animation: 'spin 1s linear infinite'
          }}></div>
        </div>
      ) : filteredMovies.length === 0 ? (
        <div style={{ textAlign: 'center', padding: '40px 0', color: '#a7a3c4' }}>
          Không tìm thấy bộ phim nào phù hợp.
        </div>
      ) : (
        <div className="movie-grid">
          {filteredMovies.map(movie => (
            <div 
              key={movie.id} 
              className="movie-card"
              onClick={() => onSelectMovie(movie.id)}
            >
              <div className="movie-img-container">
                <img 
                  src={movie.img_url || 'https://via.placeholder.com/300x450'} 
                  alt={movie.tieu_de} 
                  className="movie-img"
                  onError={(e) => {
                    e.target.onerror = null; 
                    e.target.src = 'https://via.placeholder.com/300x450?text=No+Poster';
                  }}
                />
                {movie.gia_han_tuoi && (
                  <span className="movie-age-badge">T{movie.gia_han_tuoi}</span>
                )}
              </div>
              <div className="movie-info">
                <h3 className="movie-title">{movie.tieu_de}</h3>
                <div className="movie-meta">
                  <span>🍿 {movie.genre}</span>
                  <span>⏱️ {movie.thoi_luong_phim} phút</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Define spinner keyframes */}
      <style>{`
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  );
};

export default Home;
