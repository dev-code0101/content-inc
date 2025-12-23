import React, { memo } from 'react';
import PostCard from './PostCard';

const PostList = memo(({ posts }) => {
  if (!posts || posts.length === 0) {
    return <div className="p-6 text-center text-gray-500">No posts</div>;
  }

  return (
    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      {posts.map(p => <PostCard key={p.id || p.slug} post={p} />)}
    </div>
  );
});

export default PostList;
