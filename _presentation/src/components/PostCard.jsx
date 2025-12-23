import React, { memo } from 'react';
import { Link } from 'react-router-dom';

const PostCard = memo(({ post }) => {
  const title = post.title;
  const excerpt = (post.content || '').slice(0, 200) + (post.content?.length > 200 ? '…' : '');
  
  return (
    <article className="bg-white shadow rounded p-4 hover:shadow-md transition">
      <h3 className="text-lg font-semibold">
        <Link to={`/post/${post.id || post.slug}`}>{title}</Link>
      </h3>
	  <img src={post.image} alt={title} className="w-full h-auto" loading="lazy" />
      <p className="text-sm text-gray-600 mt-2">{excerpt}</p>
      <div className="mt-3 text-xs text-gray-500">Status: {post.status ? 'Published' : 'Draft'}</div>
    </article>
  );
});

export default PostCard;
