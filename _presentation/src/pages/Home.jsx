import { useEffect, useState } from 'react';
import { fetchPosts } from '../api/postsApi';
import PostList from '../components/PostList';
import Spinner from '../components/Spinner';

export default function Home(){
  const [posts,setPosts] = useState([]);
  const [loading,setLoading] = useState(false);
  const [q,setQ] = useState('');

  useEffect(()=>{ load(); },[]);

  async function load(){
    setLoading(true);
    try{ const data = await fetchPosts(); setPosts(data); } finally{ setLoading(false); }
  }

  async function doSearch(e){
    e.preventDefault();
    setLoading(true);
    try{ const data = await fetchPosts(q); setPosts(data); } finally{ setLoading(false); }
  }

  return (
    <main className="max-w-6xl mx-auto px-4 py-8">
      <form onSubmit={doSearch} className="mb-6 flex gap-2">
        <input value={q} onChange={e=>setQ(e.target.value)} placeholder="Search title..." className="flex-1 px-3 py-2 border rounded" />
        <button className="px-4 py-2 bg-blue-600 text-white rounded">Search</button>
      </form>

      {loading ? <Spinner /> : <PostList posts={posts} />}
    </main>
  );
}
