import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { fetchPost } from '../api/postsApi';
import PostDetail from '../components/PostDetail';
import Spinner from '../components/Spinner';

export default function PostPage(){
  const { id } = useParams();
  const [post,setPost] = useState(null);
  const [loading,setLoading] = useState(true);

  useEffect(()=>{ load(); }, [id]);

  async function load(){
    setLoading(true);
    try{
      const data = await fetchPost(id);
      // expects API returns { original: {...}, updated: {...} } or single object
      if (data.original || data.updated) {
        setPost(data);
      } else {
        setPost({ original: data, updated: data.updated_version || null });
      }
    } finally{ setLoading(false); }
  }

  if(loading) return <Spinner />;
  if(!post) return <div className="p-6">Not found</div>;

  return (
    <main className="max-w-4xl mx-auto px-4 py-8">
      <PostDetail post={post.original || post} updatedPost={post.updated || post.updated_version} />
    </main>
  );
}
