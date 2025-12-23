import axios from 'axios';
const client = axios.create({ baseURL: import.meta.env.VITE_API_BASE_URL, timeout: 10000 });

export async function fetchPosts(q = '') {
  const params = {};
  if (q) params.q = q;
  const res = await client.get('/posts', { params });
  return res.data;
}

export async function fetchPost(idOrSlug) {
  const res = await client.get(`/posts/${idOrSlug}`);
  return res.data;
}
