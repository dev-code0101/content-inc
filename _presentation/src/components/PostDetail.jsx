import React, { memo } from 'react';

const PostDetail = memo(({ post, updatedPost }) => {
  return (
    <div className="space-y-8">
      <section className="bg-white shadow rounded p-6">
        <h1 className="text-2xl font-bold">{post.title}</h1>
        <div className="prose mt-4" dangerouslySetInnerHTML={{ __html: post.content }} />
      </section>

      {updatedPost && (
        <section className="bg-white shadow rounded p-6">
          <h2 className="text-xl font-semibold">Updated Version</h2>
		  <img src={post.image} alt={post.title} className="w-full h-auto" loading="lazy" />
          <div className="prose mt-4" dangerouslySetInnerHTML={{ __html: updatedPost.content }} />
        </section>
      )}
    </div>
  );
});

export default PostDetail;
