"""Forkville logo geometry: python3 tools/icon_svg.py writes icons/favicon.svg (then run tools/make_icons.py).

An isometric low-poly restaurant like the ones in the game: cream walls, red-and-cream striped awnings, lit windows,
a wooden door, a terracotta roof with a round golden sign with a fork, a terrace table under a green parasol, a street
lamp and a bush, on a lawn slab under a sunny sky.
"""
from pathlib import Path
import math
R=(0.866,-0.5); L=(-0.866,-0.5)
def add(p,v,k=1): return (p[0]+v[0]*k, p[1]+v[1]*k)
def up(p,h): return (p[0], p[1]-h)
def pts(*ps): return ' '.join(f'{x:.2f},{y:.2f}' for x,y in ps)
def poly(ps, fill, extra=''): return f'<polygon points="{pts(*ps)}" fill="{fill}" {extra}/>'
out=[]
# ground slab (lawn + paving)
G0=(31,57.5); ga, gb = 25, 22
GA=add(G0,R,ga); GB=add(G0,L,gb); GC=add(GA,L,gb)
th=2.2
out.append(poly([G0,GA,up(GA,-th),up(G0,-th)],'#4E8A34'))
out.append(poly([G0,GB,up(GB,-th),up(G0,-th)],'#5E9A3E'))
out.append(poly([G0,GA,GC,GB],'url(#lawn)'))
# terrace paving in front of the right face
P0=(31.5,53); a,b,h=20,14.5,16.5
A=add(P0,R,a); B=add(P0,L,b)
T0=add(P0,R,0); 
pav=[add(P0,L,-0.5), add(A,L,-0.5), add(add(A,L,-0.5),(0.866*0,0),0)]
# paving strip: from building front edge outward (toward viewer = -L direction)
F0=P0; F1=A; F2=add(A,L,-5.5); F3=add(P0,L,-5.5)
out.append(poly([F0,F1,F2,F3],'#E9DCC6'))
for k in range(1,5):
    s=add(P0,R,a*k/5); e=add(s,L,-5.5)
    out.append(f'<line x1="{s[0]:.2f}" y1="{s[1]:.2f}" x2="{e[0]:.2f}" y2="{e[1]:.2f}" stroke="#D6C6AB" stroke-width=".5"/>')
# building
P0t,At,Bt=up(P0,h),up(A,h),up(B,h); C=add(At,L,b)
out.append(poly([P0,B,Bt,P0t],'url(#wallL)'))   # left face
out.append(poly([P0,A,At,P0t],'url(#wallR)'))   # right face (front)
# plinth
out.append(poly([P0,A,up(A,1.6),up(P0,1.6)],'#D8C3A2'))
out.append(poly([P0,B,up(B,1.6),up(P0,1.6)],'#C6AE8B'))
# roof slab with parapet
pr=1.6
out.append(poly([P0t,At,up(At,pr),up(P0t,pr)],'#B84A33'))
out.append(poly([P0t,Bt,up(Bt,pr),up(P0t,pr)],'#9E3B2A'))
out.append(poly([up(P0t,pr),up(At,pr),up(C,pr),up(Bt,pr)],'#E0654A'))
inner=lambda p,q,k: (p[0]+(q[0]-p[0])*k,p[1]+(q[1]-p[1])*k)
cen=((P0t[0]+C[0])/2,(P0t[1]+C[1])/2-pr)
ins=[inner(up(x,pr),cen,0.18) for x in (P0t,At,C,Bt)]
out.append(poly(ins,'#C9533C'))
# door on right face
def Q(t,hh): return up(add(P0,R,a*t),hh)
out.append(poly([Q(.6,0),Q(.79,0),Q(.79,9.2),Q(.6,9.2)],'#6B3A24'))
out.append(poly([Q(.62,.6),Q(.77,.6),Q(.77,8.6),Q(.62,8.6)],'#8A5232'))
out.append(poly([Q(.64,5.4),Q(.75,5.4),Q(.75,8.0),Q(.64,8.0)],'url(#lit)'))
d=Q(.735,4); out.append(f'<circle cx="{d[0]:.2f}" cy="{d[1]:.2f}" r=".45" fill="#FFD77A"/>')
# shop window right face
out.append(poly([Q(.1,2.6),Q(.48,2.6),Q(.48,8.6),Q(.1,8.6)],'#FFD46B'))
out.append(poly([Q(.12,3.2),Q(.46,3.2),Q(.46,8.0),Q(.12,8.0)],'url(#lit)'))
m1,m2=Q(.29,2.6),Q(.29,8.6); out.append(f'<line x1="{m1[0]:.2f}" y1="{m1[1]:.2f}" x2="{m2[0]:.2f}" y2="{m2[1]:.2f}" stroke="#E9A93A" stroke-width=".6"/>')
# left face window
def QL(t,hh): return up(add(P0,L,b*t),hh)
out.append(poly([QL(.25,4),QL(.72,4),QL(.72,9.4),QL(.25,9.4)],'#F2B84E'))
out.append(poly([QL(.28,4.6),QL(.69,4.6),QL(.69,8.8),QL(.28,8.8)],'url(#lit)'))
m1,m2=QL(.485,4),QL(.485,9.4); out.append(f'<line x1="{m1[0]:.2f}" y1="{m1[1]:.2f}" x2="{m2[0]:.2f}" y2="{m2[1]:.2f}" stroke="#E9A93A" stroke-width=".6"/>')
m1,m2=QL(.25,6.7),QL(.72,6.7); out.append(f'<line x1="{m1[0]:.2f}" y1="{m1[1]:.2f}" x2="{m2[0]:.2f}" y2="{m2[1]:.2f}" stroke="#E9A93A" stroke-width=".6"/>')
# awning on right face: slopes out toward the viewer (-L direction)
n=6; top=13.4; drop=3.0; out_k=2.6
for i in range(n):
    t0,t1=i/n,(i+1)/n
    p1=Q(t0,top); p2=Q(t1,top)
    p3=add(up(add(P0,R,a*t1),top-drop),L,-out_k); p4=add(up(add(P0,R,a*t0),top-drop),L,-out_k)
    col='#E0483C' if i%2==0 else '#FFF4E4'
    out.append(poly([p1,p2,p3,p4],col))
    # scallop
    c=((p3[0]+p4[0])/2,(p3[1]+p4[1])/2)
    out.append(poly([p4,p3,add(p3,(0,0.9)),add(p4,(0,0.9))],col)) # the valance
# awning on left face (smaller, over the window)
for i in range(4):
    t0,t1=0.2+i*0.14,0.2+(i+1)*0.14
    p1=QL(t0,11.4); p2=QL(t1,11.4); p3=add(QL(t1,9.6),R,-1.8); p4=add(QL(t0,9.6),R,-1.8)
    col='#C73B31' if i%2==0 else '#EFE0CB'
    out.append(poly([p1,p2,p3,p4],col))
# roof sign: round board with a fork, on two posts
sx,sy=cen[0]+0.3,cen[1]-1.0
out.append(f'<rect x="{sx-3.2:.2f}" y="{sy-6:.2f}" width=".9" height="6.5" fill="#5A3825"/><rect x="{sx+2.3:.2f}" y="{sy-6:.2f}" width=".9" height="6.5" fill="#5A3825"/>')
out.append(f'<circle cx="{sx:.2f}" cy="{sy-11:.2f}" r="7.4" fill="#3B2418"/><circle cx="{sx:.2f}" cy="{sy-11:.2f}" r="6.4" fill="url(#gold)"/>')
fx,fy=sx,sy-11
out.append(f'<g fill="#3B2418"><rect x="{fx-.38:.2f}" y="{fy-0.2:.2f}" width=".76" height="4.6" rx=".38"/><path d="M{fx-1.75:.2f} {fy-1.4:.2f}h3.5v.4c0 1.05-.8 1.75-1.75 1.75s-1.75-.7-1.75-1.75z"/>' + ''.join(f'<rect x="{fx-1.75+i*1.4:.2f}" y="{fy-4.4:.2f}" width=".7" height="3.3" rx=".35"/>' for i in range(3)) + '</g>')  # a slender three-tined fork
# terrace: table with parasol and two chairs in front of the window
tb=add(Q(.22,0),L,-4.6)
out.append(f'<ellipse cx="{tb[0]:.2f}" cy="{tb[1]+.3:.2f}" rx="3.2" ry="1.4" fill="#000" opacity=".12"/>')
out.append(f'<rect x="{tb[0]-.3:.2f}" y="{tb[1]-9.5:.2f}" width=".6" height="9.5" fill="#FFF4E4"/>')
out.append(f'<ellipse cx="{tb[0]:.2f}" cy="{tb[1]-3:.2f}" rx="2.4" ry="1.1" fill="#FFFFFF"/><rect x="{tb[0]-.35:.2f}" y="{tb[1]-3:.2f}" width=".7" height="3" fill="#7B4E2C"/>')
pa=(tb[0],tb[1]-10)
out.append(f'<path d="M{pa[0]-5:.2f} {pa[1]+3:.2f}Q{pa[0]:.2f} {pa[1]-2.2:.2f} {pa[0]+5:.2f} {pa[1]+3:.2f}z" fill="#7A8B3A"/><path d="M{pa[0]-1.7:.2f} {pa[1]+1.9:.2f}Q{pa[0]:.2f} {pa[1]-1.6:.2f} {pa[0]+1.7:.2f} {pa[1]+1.9:.2f}z" fill="#9CB05A"/>')
for dx in (-3.1,3.1):
    out.append(f'<rect x="{tb[0]+dx-.8:.2f}" y="{tb[1]-2.4:.2f}" width="1.6" height="2.4" rx=".3" fill="#B07A4A"/><rect x="{tb[0]+dx-.8:.2f}" y="{tb[1]-4.4:.2f}" width="{1.6:.2f}" height="2" rx=".3" fill="#9C6B3F"/>')
# a bush and a lamp
bu=add(B,R,-0.2); bu=add(bu,L,-2.6)
out.append(f'<circle cx="{bu[0]+1:.2f}" cy="{bu[1]-1.6:.2f}" r="2.3" fill="#4F8A34"/><circle cx="{bu[0]+2.4:.2f}" cy="{bu[1]-2.6:.2f}" r="1.6" fill="#6FAF45"/>')
lp=add(A,L,-4.4)
out.append(f'<rect x="{lp[0]-.35:.2f}" y="{lp[1]-12:.2f}" width=".7" height="12" fill="#2F3A3A"/><circle cx="{lp[0]:.2f}" cy="{lp[1]-12.6:.2f}" r="3" fill="#FFE7A8" opacity=".35"/><circle cx="{lp[0]:.2f}" cy="{lp[1]-12.6:.2f}" r="1.3" fill="#FFF1C9"/>')
defs='''<defs><linearGradient id="fvbg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8ED7E6"/><stop offset=".62" stop-color="#FFE3B8"/><stop offset="1" stop-color="#FFC48A"/></linearGradient>
<linearGradient id="lawn" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#9CD067"/><stop offset="1" stop-color="#77B348"/></linearGradient>
<linearGradient id="wallR" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFF6E8"/><stop offset="1" stop-color="#F6E2C6"/></linearGradient>
<linearGradient id="wallL" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#EBD6B8"/><stop offset="1" stop-color="#DCC3A0"/></linearGradient>
<linearGradient id="lit" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFF2C2"/><stop offset="1" stop-color="#FFC857"/></linearGradient>
<linearGradient id="gold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FFE08A"/><stop offset="1" stop-color="#F2B33D"/></linearGradient></defs>'''
clouds='<g fill="#FFFFFF" opacity=".85"><ellipse cx="13" cy="12" rx="6" ry="2.6"/><ellipse cx="16.5" cy="10.4" rx="3.6" ry="2.6"/><ellipse cx="51" cy="17" rx="5" ry="2.2"/><ellipse cx="53.6" cy="15.6" rx="3" ry="2.1"/></g><circle cx="51" cy="7.5" r="3.4" fill="#FFF3C4" opacity=".9"/>'
svg=f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">{defs}<rect width="64" height="64" rx="15" fill="url(#fvbg)"/>{clouds}'+''.join(out)+'</svg>'
open(Path(__file__).resolve().parent.parent / 'icons' / 'favicon.svg', 'w').write(svg.replace('\n',''))
