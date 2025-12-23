import { Link } from 'react-router-dom';

export default function Header(){ return (
  <header className="bg-white shadow-sm">
    <div className="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <Link to="/" className="text-xl font-semibold">Articles UI</Link>
    </div>
  </header>
);}
