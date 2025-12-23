import { Link } from 'react-router-dom';

export default function PostCard({ post }){
  const title = post.title;
  const excerpt = (post.content || '').slice(0,200) + (post.content?.length>200?'…':'');
  return (
    <article className="bg-white shadow rounded p-4 hover:shadow-md transition">
      <h3 className="text-lg font-semibold"><Link to={`/post/${post.id || post.slug}`}>{title}</Link></h3>
      <p className="text-sm text-gray-600 mt-2">{excerpt}</p>
      <div className="mt-3 text-xs text-gray-500">Status: {post.status ? 'Published' : 'Draft'}</div>
    </article>
  );
}
