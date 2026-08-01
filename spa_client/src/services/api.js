// api.js - React API service calling the PHP RESTful backend
const API_BASE_URL = 'http://localhost:83/webphim_hung/api';

export const getMovies = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}/get_movies.php`);
    if (!response.ok) throw new Error('Network response was not ok');
    const result = await response.json();
    return result.success ? result.data : [];
  } catch (error) {
    console.error('Error fetching movies:', error);
    return [];
  }
};

export const getMovieDetail = async (movieId) => {
  try {
    const response = await fetch(`${API_BASE_URL}/get_movie_detail.php?id=${movieId}`);
    if (!response.ok) throw new Error('Network response was not ok');
    const result = await response.json();
    return result.success ? result.data : null;
  } catch (error) {
    console.error(`Error fetching movie detail for ID ${movieId}:`, error);
    return null;
  }
};

export const getSeatsForSlot = async (slotId) => {
  try {
    const response = await fetch(`${API_BASE_URL}/get_seats.php?id_slot=${slotId}`);
    if (!response.ok) throw new Error('Network response was not ok');
    const result = await response.json();
    return result.success ? result.data : null;
  } catch (error) {
    console.error(`Error fetching seats for slot ID ${slotId}:`, error);
    return null;
  }
};
